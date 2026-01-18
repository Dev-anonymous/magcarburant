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

        if ($user->user_role == 'petrolier') {
            if ($type === 'purchase') {
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
                    $data[] = round($entity->purchases()->where('product', $el)->whereBetween('date', [$from, $to])->sum(DB::raw('unitprice * qtytm')), 3);
                    $labels[] = $el;
                }
                $chart1 = compact('labels', 'data');

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $data[] = round($entity->purchases()->where('product', $el)->whereBetween('date', [$from, $to])->sum('qtym3'), 3);
                    $labels[] = $el;
                }
                $chart2 = compact('labels', 'data');

                return compact('totalTm', 'totalM3', 'totalAmount', 'chart1', 'chart2');
            }
            if ($type === 'sale') {
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
                    $data[] =  round($entity->sales()->where('product', $el)->whereBetween('date', [$from, $to])->sum('lata')  / 1000, 3);
                    $labels[] = $el;
                }
                $chart1 = compact('labels', 'data');

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $data[] = round($entity->sales()->where('product', $el)->whereBetween('date', [$from, $to])->sum('lata'), 3);
                    $labels[] = $el;
                }
                $chart2 = compact('labels', 'data');

                return compact('totalLata', 'totalL15', 'chart1', 'chart2');
            }

            if ($type === 'delivery') {
                $date = request('date');
                $date = explode(' to ', $date);
                $date = array_filter($date);
                $from = @$date[0] ?? nnow()->toDateString();
                $to = @$date[1] ?? $from;

                $entity = $user->entities()->first();
                $base = $entity->deliveries()->whereBetween('date', [$from, $to]);

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $data[] =  round($entity->deliveries()->where('product', $el)->whereBetween('date', [$from, $to])->sum('lata'), 3);
                    $labels[] = $el;
                }
                $chart1 = compact('labels', 'data');

                $labels = [];
                $data = [];
                foreach (mainWays() as $el) {
                    $data[] = round($entity->deliveries()->where('way', $el)->whereBetween('date', [$from, $to])->sum('lata'), 3);
                    $labels[] = $el;
                }
                $chart2 = compact('labels', 'data');

                return compact('chart1', 'chart2');
            }
            if ($type === 'greatbook') {
                return $this->greatBookData();
            }

            if ($type === 'greatbookcr') {
                return $this->greatBookCrData();
            }

            if ($type === 'balance') {
                $data = $this->greatBookData();
                $zones = (array) request('zone');
                $fuels = (array) request('fuel');

                $dhead = $data['head'];
                $drows = $data['rows'];

                $head = array_merge(
                    [['label' => 'PRODUITS', 'class' => 'title']],
                    array_map(function ($z) {
                        return [
                            'label' => $z,
                            'class' => 'title'
                        ];
                    }, $zones),
                    [['label' => 'TOTAL', 'class' => 'title']]
                );

                $rows = [];

                $title = items();

                $tabv = [];
                $tabt = [];
                foreach ($zones as $z) {
                    $tabv["v_$z"] = 0;
                    $tabt["t_$z"] = 0;
                }

                foreach ($title as $ti) {
                    foreach ($fuels as $fuel) {
                        $line = [];
                        $line[] = ['label' => $fuel];
                        $tot2 = 0;
                        foreach ($zones as $zone) {
                            $tot = 0;
                            $index = findIndexByLabel($dhead, $ti->label);
                            if (null !== $index) {
                                foreach ($drows as $r) {
                                    $v = (float) @$r[$index]['vv'];
                                    $zo = $r[4]['v'];
                                    $pro = $r[5]['v'];
                                    abort_if(!in_array($pro, mainfuels()), 422, "Can't process: Invalid product : $pro");
                                    if ($pro === $fuel && $zone === $zo) {
                                        $tot += $v;
                                    }
                                }
                                $line[] = ['label' => v($tot)];
                                $tot2  += $tot;
                                $v0 = (float) @$tabv["v_$zone"];
                                $tabv["v_$zone"] =  $v0 + $tot;
                            }
                        }
                        $line[] = ['label' => v($tot2)];
                        $rows[] = $line;
                    }

                    $line0 = [];
                    $line0[] = [
                        'label' => $ti->label,
                        'class' => 'title1',
                        'href' => route('provider.accounting', ['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val]),
                        'title' => "Afficher les valeurs $ti->label de toutes les zones",
                    ];
                    $t0 = 0;

                    foreach ($tabv as $k => $v) {
                        $z = array_values(array_filter(explode('v_', $k)))[0];
                        $line0[] = [
                            'label' => v($v),
                            'class' => 'title1',
                            'href' => route('provider.accounting', ['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val, 'z' => $z]),
                            'title' => "Afficher les valeurs $ti->label de la zone $z",
                        ];
                        $t0 += $v;
                        $tabv[$k] = 0;

                        $v0 = (float) $tabt["t_$z"];
                        $tabt["t_$z"] = $v0 + $v;
                    }

                    $line0[] = [
                        'label' => v($t0),
                        'class' => 'title1',
                        'href' => route('provider.accounting', ['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val]),
                        'title' => "Afficher les valeurs $ti->label de toutes les zones",
                    ];
                    $rows[] = $line0;
                }

                $line0 = [];
                $line0[] = [
                    'label' => "TOTAL GENERAL",
                    'class' => 'title1',
                    'href' => route('provider.accounting', ['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2')]),
                    'title' => 'Afficher les détails pour toutes les zones'
                ];
                $t0 = 0;
                foreach ($tabt as $k => $v) {
                    $z = array_values(array_filter(explode('t_', $k)))[0];
                    $line0[] = [
                        'label' => v($v),
                        'class' => 'title1',
                        'href' => route('provider.accounting', ['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'z' => $z]),
                        'title' => "Afficher le Total de la zone $z",
                    ];
                    $t0 += $v;
                }

                $line0[] = [
                    'label' => v($t0),
                    'class' => 'title1',
                    'href' => route('provider.accounting', ['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2')]),
                    'title' => 'Afficher les détails pour toutes les zones'
                ];
                $rows[] = $line0;
                array_unshift($rows, $head);

                return response()->json(['rows' => $rows, 'errors' => $data['errors']]);
            }

            if ($type === 'balancecr') {
                $data = $this->greatBookData();
                $zones = (array) request('zone');
                $fuels = (array) request('fuel');

                $dhead = $data['head'];
                $drows = $data['rows'];

                $head = array_merge(
                    [['label' => 'ITEMS', 'class' => 'title']],
                    array_map(function ($z) {
                        return [
                            'label' => $z,
                            'class' => 'title'
                        ];
                    }, $fuels),
                    [['label' => 'TOTAL', 'class' => 'title']]
                );

                $n = count($head);
                // array_unshift($head, [['label' => 'PERTES ET MANQUES A GAGNER', 'colspan' => $n]]);
                // array_unshift($head, [['label' => 'CROISEMENT DES CREANCES USD', 'colspan' => $n]]);

                $tabVar = [];
                foreach ($fuels as $z) {
                    $tabVar["pmag_ste_petro_$z"] = 0;
                    $tabVar["tot_pmag_$z"] = 0;
                    $tabVar["livr_excedent_$z"] = 0;
                    $tabVar["tot_creance_ste_$z"] = 0;
                }

                $rows = [];

                $line0 = [];
                $line0[] = [
                    'label' => 'PMAG DES SOCIETES PETROLIERES',
                    // 'class' => 'title1',
                    // 'href' => route('provider.accounting', ['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val]),
                    // 'title' => "Afficher les valeurs $ti->label de toutes les zones",
                ];

                $tg = 0;
                $tot = 0;
                $title = items();
                $lb = [];
                array_map(function ($item) use (&$lb) {
                    $lb[] = $item->label;
                }, $title);
                $lb = implode(' + ', $lb);

                foreach ($fuels as $k => $fuel) {
                    foreach ($title as $ti) {
                        $index = findIndexByLabel($dhead, $ti->label);
                        if (null !== $index) {
                            foreach ($drows as $r) {
                                $v = (float) @$r[$index]['vv'];
                                $zo = @$r[4]['v'];
                                $pro = @$r[5]['v'];
                                abort_if(!in_array($zo, mainWays()), 422, "Can't process: Invalid zone : $zo");
                                abort_if(!in_array($pro, mainfuels()), 422, "Can't process: Invalid product : $pro");
                                if ($pro === $fuel && in_array($zo, $zones)) {
                                    $tot += $v;
                                }
                            }
                        }
                    }

                    incr($tabVar, "pmag_ste_petro_$fuel", $tot);
                    incr($tabVar, "tot_pmag_$fuel", $tot);

                    $tg += $tot;
                    $line0[] = [
                        'label' => v($tot),
                        // 'class' => 'title1',
                        // 'href' => route('provider.accounting', ['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val]),
                        'title' => "Somme de $lb du produit $fuel pour les zones : " . implode(', ', $zones),
                    ];
                }

                $line0[] = [
                    'label' => v($tg),
                    'class' => 'title1',
                    // 'href' => route('provider.accounting', ['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val]),
                    'title' => "Total pour tous les produits.",
                ];

                $rows[] = $line0;

                $line00 = [];
                foreach ($line0 as $k => $v) {
                    if (0 == $k) {
                        $line00[] = [
                            'label' => "TOTAL PMAG",
                            'class' => 'title1',
                        ];
                        continue;
                    }
                    $v['class'] = 'title1';
                    $line00[] = $v;
                }

                $rows[] = $line00;


                $line0 = [];
                $line0[] = [
                    'label' => 'LIVRAISONS EXCÉDENTAIRES',
                ];

                foreach ($fuels as $k => $fuel) {
                    $line0[] = [
                        'label' => '0',
                    ];
                }
                $line0[] = [
                    'label' => '0',
                ];
                $rows[] = $line0;


                $line0 = [];
                $line0[] = [
                    'label' => 'TOTAL CREANCE  DE LA SOCIETE SUR L\'ETAT',
                    'class' => 'title1',
                ];

                $tg = 0;
                foreach ($fuels as $fuel) {
                    $tpmag = $tabVar["tot_pmag_$fuel"];
                    $livrex = $tabVar["livr_excedent_$fuel"];
                    $t = $tpmag + $livrex;
                    $line0[] = [
                        'label' => v($t),
                        'class' => 'title1',
                        'title' => "TOTAL PMAG + LIVRAISONS EXCÉDENTAIRES du produit $fuel"
                    ];
                    $tg += $t;
                }

                $line0[] = [
                    'label' => v($tg),
                    'class' => 'title1',
                ];

                $rows[] = $line0;


                $line0 = [];
                $line0[] = [
                    'label' => "CREANCES DE L'ETAT SUR LA SOCIETE",
                    'class' => 'title1',
                ];
                foreach ($fuels as $fuel) {
                    $line0[] = [
                        'label' => "",
                        'class' => 'title1',
                    ];
                }
                $line0[] = [
                    'label' => "",
                    'class' => 'title1',
                ];
                $rows[] = $line0;

                // dd($tabVar);

                array_unshift($rows, $head);

                return response()->json(['rows' => $rows, 'errors' => $data['errors']]);
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
            $structure = $entity->structureprices()->where(function ($q) use ($saledate) {
                $q->where(function ($q) use ($saledate) {
                    $q->where('from', '<=', $saledate)->where('to', '>=', $saledate);
                })->orWhere(function ($q) use ($saledate) {
                    $q->whereNull('to')->where('from', '<=', $saledate);
                });
            })->orderByDesc('from')->first();

            $m3 = ((float) $e->lata) / 1000;

            $zone = $e->way;
            $fuel = $e->product;

            if (!$structure) {
                $errors[] = "Aucune structure de prix n'a été trouvée pour la vente #$e->id du {$e->date?->format('d-m-Y')} ($fuel, $zone)";
            }

            $fuelObj = Fuel::where(compact('fuel'))->first();

            $startOfMonth = $saledate->copy()->startOfMonth();
            $endOfMonth   = $saledate->copy()->endOfMonth();

            $pmfc_reel = (float) ($entity->purchases()->where(function ($q) use ($fuel) {
                $q->where('product', $fuel);
            })->where('way', $zone)->whereBetween('date', [$startOfMonth, $endOfMonth])->avg('unitprice') ?? 0);

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
                ['v' => v($pmfc_reel), 'class' => 'bigtitle', 'title' => "Moyenne mensuelle du prix unitaire d'achat $fuel (zone $zone) du {$startOfMonth->format('d-m-Y')} au {$endOfMonth->format('d-m-Y')}"],
                ['v' => v($pmfc_struct)],
                ['v' => v($ecart_pmf), 'class' => 'bigtitle', 'title' => "PMFC REEL - PMFC STRUCTURE"],
                ['v' => v($pmag_pmfc_socom), 'vv' => $pmag_pmfc_socom,  'class' => 'bigtitle', 'title' => "ECART PMFC * M3"],
                ['v' => v($pmag_marge_socom), 'vv' => $pmag_marge_socom, 'class' => 'bigtitle', 'title' => '10% de PMAG PMFC SOCOM'],
                ['v' => v($tx_reel)],
                ['v' => v($tx_str)],
                ['v' => v($variation_change), 'class' => 'bigtitle', 'title' => '(Taux Réel - Taux Structure)/Taux Réel '],
                ['v' => v($charge_socom_str)],
                ['v' => v($marge_socom_str)],
                ['v' => v($ecart_change), 'class' => 'bigtitle', 'title' => '(PMFC Structure + Charge SOCOM Structure + Marge SOCOM Structure ) * Variation Change'],
                ['v' => v($pmag_change_socom), 'vv' => $pmag_change_socom, 'class' => 'bigtitle', 'title' => 'Ecart Change * M3'],
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

    private function greatBookCrData()
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
            ['label' => 'Stock de Sécurité 1', 'class' => "bigtitle"],
            ['label' => 'Stock de Sécurité 2', 'class' => "bigtitle"],
            ['label' => 'Stock de Sécurité', 'class' => "bigtitle"],
            ['label' => 'Montant Stock de Sécurité', 'class' => "bigtitle"],
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
            $structure = $entity->structureprices()->where(function ($q) use ($saledate) {
                $q->where(function ($q) use ($saledate) {
                    $q->where('from', '<=', $saledate)->where('to', '>=', $saledate);
                })->orWhere(function ($q) use ($saledate) {
                    $q->whereNull('to')->where('from', '<=', $saledate);
                });
            })->orderByDesc('from')->first();

            $m3 = ((float) $e->lata) / 1000;

            $zone = $e->way;
            $fuel = $e->product;

            if (!$structure) {
                $errors[] = "Aucune structure de prix n'a été trouvée pour la vente #$e->id du {$e->date?->format('d-m-Y')} ($fuel, $zone)";
            }

            $fuelObj = Fuel::where(compact('fuel'))->first();

            $startOfMonth = $saledate->copy()->startOfMonth();
            $endOfMonth   = $saledate->copy()->endOfMonth();

            $pmfc_reel = (float) ($entity->purchases()->where(function ($q) use ($fuel) {
                $q->where('product', $fuel);
            })->where('way', $zone)->whereBetween('date', [$startOfMonth, $endOfMonth])->avg('unitprice') ?? 0);

            $ss_1 = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', 'Stock de Sécurité 1'))->first()?->amount;

            $ss_2 = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', 'Stock de Sécurité 2'))->first()?->amount;
            $ss = $ss_1 + $ss_2;
            $mss = $ss * $m3;

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
                ['v' => v($ss_1), 'class' => 'bigtitle', 'title' => "Stock de Sécurité 1 $fuel (zone $zone) de la structure du {$structure?->from?->format('d-m-Y')}"],
                ['v' => v($ss_2), 'class' => 'bigtitle', 'title' => "Stock de Sécurité 2 $fuel (zone $zone) de la structure du {$structure?->from?->format('d-m-Y')}"],
                ['v' => v($ss), 'class' => 'bigtitle', 'title' => "Stock de Sécurité 1 + Stock de Sécurité 2 $fuel (zone $zone)"],
                ['v' => v($mss), 'class' => 'bigtitle', 'title' => "(Stock de Sécurité 1 + Stock de Sécurité 2) * M3 $fuel (zone $zone)"],
            ];
            $rows[] = $pline;
        }

        $errors = array_values(array_unique($errors));

        if ($items == 'item1') {
            $head = array_slice($head, 0, 14);
            $t = [];
            foreach ($rows as $r) {
                $t[] = array_slice($r, 0, 14);
            }
            $rows = $t;
        }

        if ($items == 'item2') {
            $head = array_slice($head, 0, 15);
            $t = [];
            foreach ($rows as $r) {
                $t[] = array_slice($r, 0, 15);
            }
            $rows = $t;
        }

        if ($items == 'item3') {
            $head = array_slice($head, 0, 16);
            $t = [];
            foreach ($rows as $r) {
                $t[] = array_slice($r, 0, 16);
            }
            $rows = $t;
        }

        return compact('head', 'rows', 'errors');
    }
}
