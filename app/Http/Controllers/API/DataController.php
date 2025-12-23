<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fuel;
use App\Models\Label;
use App\Models\Purchase;
use App\Models\Rate;
use App\Models\Sale;
use App\Models\Structureprice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    function dashboard()
    {
        $user = auth()->user();

        $type = request('type');

        if ($user->user_role == 'provider') {
            if ($type == 'purchase') {
                $date = request('date');
                $date = explode(' to ', $date);
                $date = array_filter($date);
                $from = @$date[0] ?? nnow()->toDateString();
                $to = @$date[1] ?? $from;

                $entity = $user->entities()->first();
                $base = $entity->purchases()->whereBetween('date', [$from, $to]);

                $totalTm = v((clone $base)->sum('qtytm'));
                $totalM3 = v((clone $base)->sum('qtym3'));
                $totalAmount = v((clone $base)->sum(DB::raw('unitprice * qtytm')));

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $data[] =  $entity->purchases()->where('product', $el)->whereBetween('date', [$from, $to])->sum(DB::raw('unitprice * qtytm'));
                    $labels[] = $el;
                }
                $chart1 = compact('labels', 'data');

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $data[] =  $entity->purchases()->where('product', $el)->whereBetween('date', [$from, $to])->sum('qtym3');
                    $labels[] = $el;
                }
                $chart2 = compact('labels', 'data');

                return compact('totalTm', 'totalM3', 'totalAmount', 'chart1', 'chart2');
            }
            if ($type == 'sale') {
                $date = request('date');
                $date = explode(' to ', $date);
                $date = array_filter($date);
                $from = @$date[0] ?? nnow()->toDateString();
                $to = @$date[1] ?? $from;

                $entity = $user->entities()->first();
                $base = $entity->sales()->whereBetween('date', [$from, $to]);

                $totalLata = v((clone $base)->sum('lata'));
                $totalL15 = v((clone $base)->sum('l15'));
                // $totalDensity = v((clone $base)->sum('density'));

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $data[] =  $entity->sales()->where('product', $el)->whereBetween('date', [$from, $to])->sum('lata')  / 1000;
                    $labels[] = $el;
                }
                $chart1 = compact('labels', 'data');

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $data[] =  $entity->sales()->where('product', $el)->whereBetween('date', [$from, $to])->sum('lata');
                    $labels[] = $el;
                }
                $chart2 = compact('labels', 'data');

                return compact('totalLata', 'totalL15', 'chart1', 'chart2');
            }
            if ($type == 'balance') {
                $from = request('date1') ?? nnow()->toDateString();
                $to = request('date2') ?? $from;

                $zone = request('zone');
                $fuel = request('fuel');
                $fuel_type = request('fuel_type');

                $entity = $user->entities()->first();

                $plages = Structureprice::where('entity_id', $entity->id)
                    ->where(function ($q) use ($from, $to) {
                        $q->where(function ($q) use ($from, $to) {
                            $q->where('from', '<=', $to)
                                ->where('to', '>=', $from);
                        })
                            ->orWhere(function ($q) use ($from, $to) {
                                $q->whereNull('to')
                                    ->whereBetween('from', [$from, $to]);
                            });
                    })
                    // ->orderBy(DB::raw('`from`'), 'desc')
                    ->get();

                abort_if(!$plages->count(), 422, "Aucune structure de prix trouvée sur la plage de date sélectionnée.");

                $data = [];
                $labels = Label::whereNotIn('label', uneditable())
                    ->whereHas('fuelprices', function ($q) use ($zone, $fuel_type) {
                        $q->whereHas('zone', function ($q) use ($zone) {
                            $q->where('zone', $zone);
                        });
                        $q->whereHas('fuel', function ($q) use ($fuel_type) {
                            $q->where('fuel_type', $fuel_type);
                        });
                    })
                    ->orderBy('tag')
                    ->get();

                $fuelOb =  Fuel::where('fuel', $fuel)->first();

                foreach ($plages as $str) {
                    $strfrom = $str->from;
                    $strto = $str->to ?? nnow();

                    $tab = [];
                    $tot = 0;
                    foreach ($labels as $lab) {
                        $line = [];
                        $line['label'] = $lab->label;

                        $fprice = $str->fuelprices()->whereHas('zone', function ($q) use ($zone) {
                            if ($zone) {
                                $q->where('zone', $zone);
                            }
                        })->whereHas('fuel', function ($qq) use ($fuel) {
                            if ($fuel) {
                                $qq->where('fuel', $fuel);
                            }
                        })->where(['label_id' => $lab->id])->first();

                        abort_if(!$fprice, 422, "Aucun prix ($fuelOb->fuel $fuelOb->fuel_type , $lab->label) trouvé sur la structure de prix de la date sélectionnée (ZONE $zone, structure $str->name #$str->id).");

                        $amount = 0;
                        $currency = 'USD';
                        if ($fprice) {
                            $amount = (float) $fprice->amount;
                            $currency = $fprice->currency;
                        }
                        if ('A' === $lab->tag) {
                            $vol = Purchase::whereBetween('date', [$strfrom, $strto])->where('product', $fuel)->sum('qtym3');
                        } else {
                            $vol = Sale::where('way', $zone)->whereBetween('date', [$strfrom, $strto])->where('product', $fuel)->sum('lata'); // lata en litre
                            $vol /= 1000; // m3
                        }
                        $t = $vol * $amount;
                        $tot += $t;
                        $line['struct_price'] = $amount;
                        $line['struct_price_id'] = $str->id;
                        $line['tag'] = $lab->tag;
                        $line['vol'] = $vol;
                        $line['tot'] = v($t);
                        $line['zone'] = $zone;
                        $line['fuel'] = $fuel;
                        $line['date'] = "DU {$strfrom->format('d-m-Y')} AU {$strto->format('d-m-Y')}";

                        $tab[] = $line;
                    }

                    $data["$str->id###$str->name###ZONE $zone"] = $tab;
                }

                return response()->json($data);
            }

            if ($type == 'greatbook') {
                $structure = request('structure');
                $devise = request('devise');
                $zone = request('zone');
                $fuel = request('fuel');

                $user = auth()->user();
                $entity = $user->entities()->first();

                $structure = Structureprice::find($structure);
                abort_if(!$structure, 403, "Aucune strucutre de prix trouvée");
                abort_if($structure->entity_id != $entity->id, 403, "Not permit");

                $from = $structure->from ?? nnow();
                $to = $structure->to ?? nnow();

                $head = [
                    ['label' => 'ID'],
                    ['label' => 'Terminal'],
                    ['label' => 'Date'],
                    ['label' => 'Localité'],
                    ['label' => 'Voie'],
                    ['label' => 'Produit'],
                    ['label' => 'Bon de livraison'],
                    ['label' => 'Programme de livraison'],
                    ['label' => 'Client'],
                    ['label' => 'LATA'],
                    ['label' => 'L15'],
                    ['label' => 'Densité'],
                    ['label' => 'M3'],
                ];

                $rows = [];

                $labels = Label::orderBy('tag')->whereNotIn('label', uneditable())->get();
                $sales = $entity->sales()->where(function ($q) use ($fuel, $zone) {
                    if ($fuel) {
                        $q->where('product', $fuel);
                    }
                    if ($zone) {
                        $q->where('way', $zone);
                    }
                })->whereBetween('date', [$from, $to])->orderBy('date')->get();

                foreach ($sales as $e) {
                    $m3 = ((float) $e->lata) / 1000;
                    $pline = [
                        $e->id,
                        $e->terminal,
                        $e->date?->format('d-m-Y'),
                        $e->locality,
                        $e->way,
                        $e->product,
                        $e->delivery_note,
                        $e->delivery_program,
                        $e->client,
                        $e->lata,
                        $e->l15,
                        $e->density,
                        v($m3),
                    ];
                    $zone = $zone ?? $e->way;
                    $fuel = $fuel ?? $e->product;


                    $startOfMonth = $e->date->copy()->startOfMonth();
                    $endOfMonth   = $e->date->copy()->endOfMonth();
                    $pmfc_reel = (float) (Purchase::where(function ($q) use ($fuel, $zone) {
                        if ($fuel) {
                            $q->where('product', $fuel);
                        }
                        if ($zone) {
                            $q->where('way', $zone);
                        }
                    })->whereBetween('date', [$startOfMonth, $endOfMonth])->avg('qtym3') ?? 0);

                    $pmfc_struct = (float)@$structure->fuelprices()
                        ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                        ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                        ->whereHas('label', fn($q) => $q->where('label', 'PMFC en M3'))->first()?->amount;

                    dd($pmfc_reel, $zone, $fuel);

                    $pline2 = [];
                    foreach ($labels as $l) {
                        $fprice = (float)@$structure->fuelprices()
                            ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                            ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                            ->where(['label_id' => $l->id])->first()?->amount;
                        $pline2[] = $fprice;
                        $pline2[] = v($m3 * $fprice);
                    }
                    $line = [...$pline, ...$pline2];
                    $rows[] = $line;
                }
                foreach ($labels as $l) {
                    $head[] = ['label' => $l->label, 'tag' => $l->tag];
                    $head[] = ['label' => "Valeur $l->label", 'tag' => $l->tag];
                }

                return compact('head', 'rows');
            }
        }
    }
}
