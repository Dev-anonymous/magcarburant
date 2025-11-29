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
                $totalDensity = v((clone $base)->sum('density'));

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $data[] =  $entity->sales()->where('product', $el)->whereBetween('date', [$from, $to])->sum('density');
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

                return compact('totalLata', 'totalL15', 'totalDensity', 'chart1', 'chart2');
            }

            if ($type == 'balance') {
                $date = request('date');
                $date = explode(' to ', $date);
                $date = array_filter($date);
                $from = @$date[0] ?? nnow()->toDateString();
                $to = @$date[1] ?? $from;

                $devise = request('devise');
                $zone = request('zone');
                $fuel = request('fuel');

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
                $labels = Label::whereNotIn('tag', noteditable())->orderBy('tag')->get();
                $fuels = Fuel::all();

                foreach ($plages as $str) {
                    $strfrom = $str->from?->format('d-m-Y');
                    $strto = $str->to?->format('d-m-Y') ?? nnow()->toDateString();

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
                        if ($fprice?->zone->zone !== 'OUEST' && $lab->tag === 'L') {
                            continue;
                        }
                        abort_if(!$fprice, 422, "Aucun prix ($fuel, $lab->label) trouvé sur la structure de prix de la date sélectionnée (ZONE $zone, structure #$str->id).");

                        $amount = 0;
                        $currency = 'USD';
                        if ($fprice) {
                            $amount = (float) $fprice->amount;
                            $currency = $fprice->currency;
                        }
                        if ('A' === $lab->tag) {
                            $vol = Purchase::whereBetween('date', [$from, $to])->where('product', $fuel)->sum('qtym3');
                        } else {
                            $vol = Sale::whereBetween('date', [$from, $to])->where('product', $fuel)->sum('lata'); // lata en litre
                            $vol /= 1000; // m3
                        }
                        $t = $vol * $amount;
                        $tot += $t;
                        $line['struct_price'] = $amount;
                        $line['vol'] = $vol;
                        $line['tot'] = v($t);
                        $line['zone'] = $zone;
                        $line['fuel'] = $fuel;
                        $line['date'] = "DU $strfrom AU $strto";

                        $tab[] = $line;
                    }

                    $data["$str->id###$str->name###ZONE $zone"] = $tab;
                }

                return response()->json($data);
            }
        }
    }

    function pricestructure()
    {
        $fuel = request('fuel');
        $zone = request('zone');
        $structure = request('structure');
        $ratetype = request('ratetype') ?? "RÉEL"; // STRUCTURE RÉEL

        $str = Structureprice::findOrFail($structure);
        $from = $str->from?->toDateString() ?? nnow()->toDateString();
        $to = $str->to?->toDateString() ?? nnow()->toDateString(); // a maintenant

        $plages = Rate::where('type', $ratetype)
            ->where('entity_id', $str->entity_id)
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

        abort_if(!$plages->count(), 422, "Aucun TAUX $ratetype trouvé sur la période ($from ... $to) de cette structure de prix.");

        $data = [];
        $labels = Label::whereNotIn('tag', noteditable())->orderBy('tag')->get();
        $fuels = Fuel::all();

        foreach ($plages as $str) {
            $strfrom = $str->from?->format('d-m-Y');
            $strto = $str->to?->format('d-m-Y') ?? nnow()->toDateString();

            $tab = [];
            $tot = 0;
            $stprice = Structureprice::where(['entity_id' => $str->entity_id])->whereBetween('from', [$from, $to])
                ->distinct()
                ->orderBy('from', 'desc')->first(); // doit forcement renvoyer au moins 1 element

            abort_if(!$stprice, 422, "Aucune structure trouvée.");

            foreach ($labels as $lab) {
                $line = [];
                $line['label'] = $lab->label;

                $fprice = $stprice->fuelprices()
                    ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                    ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                    ->where(['label_id' => $lab->id])->first();

                if ($fprice?->zone->zone !== 'OUEST' && $lab->tag === 'L') {
                    continue;
                }
                abort_if(!$fprice, 422, "Aucun prix ($fuel, $lab->label) trouvé sur la structure de prix de la date sélectionnée (ZONE $zone, structure #$str->id).");

                $amount = 0;
                $currency = 'USD';
                $tx = (float) $str->usd_cdf;
                $currency = 'CDF'; // lol, yes
                if ($fprice) {
                    $amount = (float) $fprice->amount;
                    // $currency = $fprice->currency;
                }
                $amount *= $tx;

                if ('A' === $lab->tag) {
                    $vol = Purchase::whereBetween('date', [$from, $to])->where('product', $fuel)->sum('qtym3');
                } else {
                    $vol = Sale::whereBetween('date', [$from, $to])->where('product', $fuel)->sum('lata'); // lata en litre
                    $vol /= 1000; // m3
                }
                $t = $vol * $amount;
                $tot += $t;
                $line['struct_price'] = v($amount);
                $line['vol'] = $vol;
                $line['tot'] = v($t);
                $line['zone'] = $zone;
                $line['fuel'] = $fuel;
                $line['date'] = "DU $strfrom AU $strto | TAUX $ratetype : 1 USD = " . (v($tx)) . " CDF";
                $tab[] = $line;
            }

            $data["$str->id"] = $tab;
        }
        return response()->json($data);
    }
}
