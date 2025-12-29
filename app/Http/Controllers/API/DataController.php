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
use Illuminate\Support\Facades\Route;

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
            if ($type == 'greatbook') {
                return $this->greatBookData();
            }

            if ($type == 'balance') {

                $data = $this->greatBookData();
                $dhead = $data['head'];
                $drows = $data['rows'];

                $head = ['PRODUITS', ...mainWays(), 'TOTAL'];

                $title = ['PMAG PMFC SOCOM', 'PMAG MARGE SOCOM', 'PMAG CHANGE SOCOM'];

                foreach ($title as $ti) {
                    foreach (mainWays() as $zone) {
                        $index = array_search($ti, array_column($head, 'label'), true);

                        dd($dhead, $index, $dhead);
                    }
                }


                dd($head,);

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
        }
    }

    private function greatBookData()
    {
        $reqzone = (array) request('zone');
        $reqfuel = (array) request('fuel');
        $items = request('items');

        $user = auth()->user();
        $entity = $user->entities()->first();

        $from = request('date1') ?? nnow()->toDateString();
        $to = request('date2') ?? nnow()->toDateString();;

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
        $errors = [];

        $labels = [
            ['label' => 'PMFC REEL'],
            ['label' => 'PMFC STRUCTURE'],
            ['label' => 'ECART PMF'],
            ['label' => 'PMAG PMFC SOCOM', 'class' => "bigtitle"],
            ['label' => 'PMAG MARGE SOCOM', 'class' => "bigtitle"],
            ['label' => 'TAUX REEL BCC'],
            ['label' => 'TAUX STRUCTURE'],
            ['label' => 'Variation  Change(%)'],
            ['label' => 'CHARGE SOCOM STRUCT'],
            ['label' => 'Marge SOCOM STRUCT'],
            ['label' => 'ECART CHANGE'],
            ['label' => 'PMAG CHANGE SOCOM', 'class' => "bigtitle"],
        ];

        $head = [...$head, ...$labels];

        $sales = $entity->sales()->where(function ($q) use ($reqfuel, $reqzone) {
            if (count($reqfuel)) {
                $q->whereIn('product', $reqfuel);
            }
            if (count($reqzone)) {
                $q->whereIn('way', $reqzone);
            }
        })->whereBetween('date', [$from, $to])->orderBy('date')->get();

        foreach ($sales as $e) {
            $saledate = $e->date;
            $structure = Structureprice::where(function ($q) use ($saledate) {
                $q->where(function ($q) use ($saledate) {
                    $q->where('from', '<=', $saledate)->where('to', '>=', $saledate);
                })->orWhere(function ($q) use ($saledate) {
                    $q->whereNull('to')->where('from', '<=', $saledate);
                });
            })->orderByDesc('from')->first();

            if (!$structure) {
                $errors[] = "Aucune structure de prix n'a été trouvée pour la vente #$e->id du {$e->date?->format('d-m-Y')} ($fuel, $zone)";
            }

            $m3 = ((float) $e->lata) / 1000;

            $zone = $e->way;
            $fuel = $e->product;
            $fuelObj = Fuel::where(compact('fuel'))->first();

            $startOfMonth = $saledate->copy()->startOfMonth();
            $endOfMonth   = $saledate->copy()->endOfMonth();

            $pmfc_reel = (float) (Purchase::where(function ($q) use ($fuel) {
                $q->where('product', $fuel);
            })->whereBetween('date', [$startOfMonth, $endOfMonth])->avg('unitprice') ?? 0);

            $pmfc_struct = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', 'PMFC en M3'))->first()?->amount;
            $ecart_pmf =  $pmfc_reel - $pmfc_struct;

            $pmag_pmfc_socom = $ecart_pmf * $m3;
            $pmag_marge_socom = $pmag_pmfc_socom * (10 / 100);

            $tx_reel = (float) $entity->rates()->where(function ($q) use ($saledate) {
                $q->where(function ($q) use ($saledate) {
                    $q->where('from', '<=', $saledate)->where('to', '>=', $saledate);
                })->orWhere(function ($q) use ($saledate) {
                    $q->whereNull('to')->where('from', '<=', $saledate);
                });
            })->orderByDesc('from')->first()?->usd_cdf;
            if (!$tx_reel) {
                $errors[] = "Aucun taux réel n'a été trouvé pour la vente #$e->id du {$e->date?->format('d-m-Y')} ($fuel, $zone)";
            }
            $tx_str = (float) $structure?->usd_cdf;
            if (!$tx_str) {
                $errors[] = "Aucun taux structure n'a été trouvé pour la vente #$e->id du {$e->date?->format('d-m-Y')} ($fuel, $zone)";
            }

            $variation_change = $tx_reel ? ($tx_reel - $tx_str) / $tx_reel : 0;

            $charge_socom_str = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', function ($q) use ($fuelObj) {
                    $q->where('fuel', $fuelObj->fuel);
                    $q->where('fuel_type', $fuelObj->fuel_type);
                })
                ->whereHas('label', fn($q) => $q->where('label', "Charges d'exploitation Sociétés commerciales"))->first()?->amount;

            if (!$charge_socom_str) {
                $errors[] = "Aucune valeur Charge SOCOM n'a été trouvée dans la strucutre de prix #$structure?->id $structure?->name du {$structure?->from?->format('d-m-Y')} ($fuel, $zone)";
            }

            $marge_socom_str = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', "Marges Sociétés Commerciales (10% PMF)"))->first()?->amount;
            if (!$marge_socom_str) {
                $errors[] = "Aucune valeur Marge SOCOM n'a été trouvée dans la strucutre de prix #$structure?->id $structure?->name du {$structure?->from?->format('d-m-Y')} ($fuel, $zone)";
            }

            $ecart_change = $variation_change ? ($pmfc_struct + $charge_socom_str + $marge_socom_str) * $variation_change : 0;

            $pmag_change_socom = $ecart_change * $m3;

            $d1 = $e->date->copy()->startOfMonth();
            $pline = [
                ['v' => $e->id],
                ['v' => $e->terminal],
                ['v' => $e->date?->format('d-m-Y')],
                ['v' => $e->locality],
                ['v' => $e->way],
                ['v' => $e->product],
                ['v' => $e->delivery_note],
                ['v' => $e->delivery_program],
                ['v' => $e->client],
                ['v' => v($e->lata)],
                ['v' => v($e->l15)],
                ['v' => v($e->density)],
                ['v' => v($m3)],
                ['v' => v($pmfc_reel), 'class' => 'bigtitle', 'title' => "Moyenne mensuelle du prix unitaire d'achat $fuel du {$startOfMonth->format('d-m-Y')} au {$endOfMonth->format('d-m-Y')}"],
                ['v' => v($pmfc_struct)],
                ['v' => v($ecart_pmf), 'class' => 'bigtitle', 'title' => "PMFC REEL - PMFC STRUCTURE"],
                ['v' => v($pmag_pmfc_socom),   'class' => 'bigtitle', 'title' => "ECART PMFC * M3"],
                ['v' => v($pmag_marge_socom),  'class' => 'bigtitle', 'title' => '10% de PMAG PMFC SOCOM'],
                ['v' => v($tx_reel)],
                ['v' => v($tx_str)],
                ['v' => v($variation_change), 'class' => 'bigtitle', 'title' => '(Taux Réel - Taux Structure)/Taux Réel '],
                ['v' => v($charge_socom_str)],
                ['v' => v($marge_socom_str)],
                ['v' => v($ecart_change), 'class' => 'bigtitle', 'title' => '(PMFC Structure + Charge SOCOM Structure + Marge SOCOM Structure ) * Variation Change'],
                ['v' => v($pmag_change_socom), 'class' => 'bigtitle', 'title' => 'Ecart Change * M3'],
            ];

            $rows[] = $pline;
        }

        $errors = array_values(array_unique($errors));

        if ($items == 'item1') {
            $head = array_slice($head, 0, 17);
            $t = [];
            foreach ($rows as $r) {
                $t[] = array_slice($r, 0, 17);
            }
            $rows = $t;
        }

        if ($items == 'item2') {
            $head = array_slice($head, 0, 18);
            $t = [];
            foreach ($rows as $r) {
                $t[] = array_slice($r, 0, 18);
            }
            $rows = $t;
        }

        if ($items == 'item3') {
            $head0 = array_slice($head, 0, 16);
            $head1 = array_slice($head, 18);
            $head = [...$head0, ...$head1];

            $t = [];
            foreach ($rows as $r) {
                $head0 = array_slice($r, 0, 16);
                $head1 = array_slice($r, 18);
                $t[] = [...$head0, ...$head1];
            }
            $rows = $t;
        }

        return compact('head', 'rows', 'errors');
    }
}
