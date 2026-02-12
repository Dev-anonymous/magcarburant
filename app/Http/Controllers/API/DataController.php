<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AverageFuelPrice;
use App\Models\Entity;
use App\Models\Fuel;
use App\Models\Label;
use App\Models\Purchase;
use App\Models\Rate;
use App\Models\Sale;
use App\Models\Structureprice;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class DataController extends Controller
{
    function dashboard()
    {
        $user = auth()->user();

        $type = request('type');

        if (in_array($user->user_role, ['petrolier', 'logisticien', 'etatique'])) {
            if ($type === 'purchase') {
                $date = request('date');
                $date = explode(' to ', $date);
                $date = array_filter($date);
                $from = @$date[0] ?? nnow()->toDateString();
                $to = @$date[1] ?? $from;

                if ($user->user_role == 'etatique') {
                    $entity  = Entity::findOrFail(request('entity_id'));
                } else {
                    $entity = $user->entities()->first();
                }
                $base = $entity->purchases()->whereBetween('date', [$from, $to])->where('from_state', from_state());

                $totalTm = v((clone $base)->sum('qtytm'));
                $totalM3 = v((clone $base)->sum('qtym3'));
                $totalAmount = v((clone $base)->sum(DB::raw('unitprice * qtytm')));

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $purchases = $entity->purchases()->where('from_state', from_state());

                    $data[] = round($purchases->where('product', $el)->whereBetween('date', [$from, $to])->sum(DB::raw('unitprice * qtytm')), 3);
                    $labels[] = $el;
                }
                $chart1 = compact('labels', 'data');

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $purchases = $entity->purchases()->where('from_state', from_state());

                    $data[] = round($purchases->where('product', $el)->whereBetween('date', [$from, $to])->sum('qtym3'), 3);
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

                if ($user->user_role == 'etatique') {
                    $entity  = Entity::findOrFail(request('entity_id'));
                } else {
                    $entity = $user->entities()->first();
                }
                $base = $entity->sales()->whereBetween('date', [$from, $to])->where('from_state', from_state());

                $from_mutuality = request('from_mutuality');

                if ($from_mutuality === "1") {
                    $base->where('from_mutuality', 1);
                }
                if ($from_mutuality === "0") {
                    $base->where('from_mutuality', 0);
                }

                $totalLata = v((clone $base)->sum('lata'));
                $totalL15 = v((clone $base)->sum('l15'));

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $query = $entity->sales()->where('from_state', from_state());

                    if ($from_mutuality === "1") {
                        $query->where('from_mutuality', 1);
                    }
                    if ($from_mutuality === "0") {
                        $query->where('from_mutuality', 0);
                    }
                    $data[] =  round($query->where('product', $el)->whereBetween('date', [$from, $to])->sum(DB::raw('lata/1000')), 3);
                    $labels[] = $el;
                }
                $chart1 = compact('labels', 'data');

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $query = $entity->sales()->where('from_state', from_state());

                    if ($from_mutuality === "1") {
                        $query->where('from_mutuality', 1);
                    }
                    if ($from_mutuality === "0") {
                        $query->where('from_mutuality', 0);
                    }
                    $data[] = round($query->where('product', $el)->whereBetween('date', [$from, $to])->sum('lata'), 3);
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
                $zones = (array) request('zones');
                $fuels = (array) request('fuels');

                if ($user->user_role == 'etatique') {
                    $entity  = Entity::findOrFail(request('entity_id'));
                } else {
                    $entity = $user->entities()->first();
                }

                $base = $entity->deliveries()->whereBetween('date', [$from, $to])->where('from_state', from_state());

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $query = $entity->deliveries()->where('from_state', from_state());

                    $data[] =  round($query->where('product', $el)->whereBetween('date', [$from, $to])->whereIn('product', $fuels)->whereIn('way', $zones)->sum('lata'), 3);
                    $labels[] = $el;
                }
                $chart1 = compact('labels', 'data');

                $labels = [];
                $data = [];
                foreach (mainWays() as $el) {
                    $query = $entity->deliveries()->where('from_state', from_state());

                    $data[] = round($query->where('way', $el)->whereBetween('date', [$from, $to])->whereIn('product', $fuels)->whereIn('way', $zones)->sum('lata'), 3);
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

            if ($type === 'greatbookfisc') {
                return $this->greatBookFiscData();
            }

            if ($type === 'greatbooklog') {
                return $this->greatBookLogData();
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
                            abort_if(is_null($index), 422, "Can't process: label \"$ti->label\" not found in greatebook");

                            foreach ($drows as $r) {
                                $v = (float) @$r[$index]['vv'];
                                $zo = $r[4]['v'];
                                $pro = $r[5]['v'];
                                abort_if(!in_array($zo, mainWays()), 422, "Can't process: Invalid zone : $zo");
                                abort_if(!in_array($pro, mainfuels()), 422, "Can't process: Invalid product : $pro");
                                if ($pro === $fuel && $zone === $zo) {
                                    $tot += round($v, 3); //
                                }
                            }
                            $line[] = ['label' => v($tot)];
                            $tot2  += $tot;
                            $v0 = (float) @$tabv["v_$zone"];
                            $tabv["v_$zone"] =  $v0 + $tot;
                        }
                        $line[] = ['label' => v($tot2)];
                        $rows[] = $line;
                    }

                    $line0 = [];
                    $line0[] = [
                        'label' => $ti->label,
                        'class' => 'title1',
                        'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val]),
                        'title' => "Afficher les valeurs $ti->label de toutes les zones",
                    ];
                    $t0 = 0;

                    foreach ($tabv as $k => $v) {
                        $z = array_values(array_filter(explode('v_', $k)))[0];
                        $line0[] = [
                            'label' => v($v),
                            'class' => 'title1',
                            'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val, 'z' => $z]),
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
                        'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val]),
                        'title' => "Afficher les valeurs $ti->label de toutes les zones",
                    ];
                    $rows[] = $line0;
                }

                $line0 = [];
                $line0[] = [
                    'label' => "TOTAL GENERAL",
                    'class' => 'title1',
                    'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2')]),
                    'title' => 'Afficher les détails pour toutes les zones'
                ];
                $t0 = 0;
                foreach ($tabt as $k => $v) {
                    $z = array_values(array_filter(explode('t_', $k)))[0];
                    $line0[] = [
                        'label' => v($v),
                        'class' => 'title1',
                        'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'z' => $z]),
                        'title' => "Afficher le Total de la zone $z",
                    ];
                    $t0 += $v;
                }

                $line0[] = [
                    'label' => v($t0),
                    'class' => 'title1',
                    'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2')]),
                    'title' => 'Afficher les détails pour toutes les zones'
                ];
                $rows[] = $line0;
                array_unshift($rows, $head);

                return response()->json(['rows' => $rows, 'errors' => $data['errors']]);
            }

            if ($type === 'balancecr') {
                if ($user->user_role == 'petrolier') {
                    $entity = $user->entities()->first();
                } else if ($user->user_role == 'etatique') {
                    $entity  = Entity::findOrFail(request('entity_id'));
                } else {
                    abort(403);
                }
                $data = $this->greatBookData();
                $zones = (array) request('zone');
                $fuels = (array) request('fuel');

                $from = request('date1') ?? nnow()->toDateString();
                $to = request('date2') ?? nnow()->toDateString();

                $dhead = $data['head'];
                $drows = $data['rows'];

                ///////////////////////

                $line0 = [];
                $line0[] = [
                    'label' => "PERTES ET MANQUES A GAGNER",
                    'class' => 'title2',
                ];
                foreach ($fuels as $fuel) {
                    $line0[] = [
                        'label' => "",
                    ];
                }
                $line0[] = [
                    'label' => "",
                ];

                $head = $line0;
                $rows = [];

                $rows[] = array_merge(
                    [['label' => 'ITEMS', 'class' => 'title']],
                    array_map(function ($z) {
                        return [
                            'label' => $z,
                            'class' => 'title'
                        ];
                    }, $fuels),
                    [['label' => 'TOTAL', 'class' => 'title']]
                );

                $tabVar = [];
                foreach ($fuels as $z) {
                    $tabVar["pmag_ste_petro_$z"] = 0;
                    $tabVar["tot_pmag_$z"] = 0;
                    $tabVar["livr_excedent_$z"] = 0;
                    $tabVar["tot_creance_ste_$z"] = 0;
                }


                ///////////////////////
                $line0 = [];
                $line0[] = [
                    'label' => 'PMAG DES SOCIETES PETROLIERES',
                    // 'class' => 'title1',
                    // 'href' => route('provider.accounting', ['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val]),
                    // 'title' => "Afficher les valeurs $ti->label de toutes les zones",
                ];

                $title = items();
                $lb = [];
                array_map(function ($item) use (&$lb) {
                    $lb[] = $item->label;
                }, $title);
                $lb = implode(' + ', $lb);

                $tot = 0;
                foreach ($fuels as $k => $fuel) {
                    $t = 0;
                    foreach ($title as $ti) {
                        $index = findIndexByLabel($dhead, $ti->label);
                        abort_if(is_null($index), 422, "Can't process: label \"$ti->label\" not found in greatebook");

                        foreach ($drows as $r) {
                            $v = (float) @$r[$index]['vv'];
                            $zo = @$r[4]['v'];
                            $pro = @$r[5]['v'];
                            abort_if(!in_array($zo, mainWays()), 422, "Can't process: Invalid zone : $zo");
                            abort_if(!in_array($pro, mainfuels()), 422, "Can't process: Invalid product : $pro");
                            if ($pro === $fuel && in_array($zo, $zones)) {
                                $t += round($v, 3); //
                            }
                        }
                    }

                    incr($tabVar, "pmag_ste_petro_$fuel", $t);
                    incr($tabVar, "tot_pmag_$fuel", $t);

                    $tot += $t;
                    $line0[] = [
                        'label' => v($t),
                        'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'fuel' => $fuel]),
                        'title' => "Somme de $lb du produit $fuel pour les zones : " . implode(', ', $zones),
                    ];
                }

                $line0[] = [
                    'label' => v($tot),
                    'class' => 'title1',
                    'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2')]),
                    'title' => "Afficher les détails PMAG de toutes les zones",
                ];

                $rows[] = $line0;

                ///////////////////////


                $line00 = [];
                foreach ($line0 as $k => $v) {
                    if (0 == $k) {
                        $line00[] = [
                            'label' => "TOTAL PMAG",
                            'class' => 'title1',
                            'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2')]),
                            'title' => "Afficher les détails PMAG de toutes les zones",
                        ];
                        continue;
                    }
                    $v['class'] = 'title1';
                    $line00[] = $v;
                }

                $rows[] = $line00;
                ///////////////////////


                $line0 = [];
                $line0[] = [
                    'label' => 'LIVRAISONS EXCÉDENTAIRES',
                    'href' => gb_href(['date1' => request('date1'), 'date2' => request('date2')], 'delivery'),
                    'title' => "Afficher les détails",
                ];

                $tot = 0;
                foreach ($fuels as $k => $fuel) {
                    $delivery = $entity->deliveries()->where('from_state', from_state())->where('product', $fuel)->whereBetween('date', [$from, $to])->sum('lata');
                    $delivery = round($delivery, 3);
                    $tot += $delivery;
                    $line0[] = [
                        'label' => v($delivery),
                        'title' => "Somme LATA des livraisons excédentaires du produit $fuel pour les zones : " . implode(', ', $zones),
                        'href' => gb_href(['date1' => request('date1'), 'date2' => request('date2'), 'fuel' => $fuel], 'delivery'),
                        'title' => "Afficher les détails des livraisons excédentaires du prduit $fuel",
                    ];
                    incr($tabVar, "livr_excedent_$fuel", $delivery);
                }
                $line0[] = [
                    'label' => v($tot),
                    'title' => "Total LATA des livraisons excédentaires des produits.",
                    'href' => gb_href(['date1' => request('date1'), 'date2' => request('date2')], 'delivery'),
                    'title' => "Afficher les détails",

                ];
                $rows[] = $line0;
                ///////////////////////


                $line0 = [];
                $line0[] = [
                    'label' => "TOTAL CREANCE  DE LA SOCIETE SUR L'ETAT",
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
                        'tag' => 'total_creance_societe',
                        'value' => $fuel,
                        'title' => "TOTAL PMAG + LIVRAISONS EXCÉDENTAIRES du produit $fuel"
                    ];
                    $tg += $t;
                }

                $line0[] = [
                    'label' => v($tg),
                    'class' => 'title1',
                    'tag' => 'total_creance_societe',
                    'value' => 'total',
                ];

                $rows[] = $line0;
                ///////////////////////


                $line0 = [];
                $line0[] = [
                    'label' => "CREANCES DE L'ETAT SUR LA SOCIETE",
                    'class' => 'title2',
                ];
                foreach ($fuels as $fuel) {
                    $line0[] = [
                        'label' => "",
                    ];
                }
                $line0[] = [
                    'label' => "",
                ];
                $rows[] = $line0;


                ///////////////////////

                $line0 = [];
                $line0[] = [
                    'label' => 'STOCK DE SÉCURITÉ COLLECTÉ NON REVERSÉ',
                    'href' => gb_href(['item' => 'cc', 'date1' => request('date1'), 'date2' => request('date2')]),
                    'title' => "Afficher les détails de tous les produits.",
                ];

                $data2 = $this->greatBookCrData();
                $dhead2 = $data2['head'];
                $drows2 = $data2['rows'];

                $tot = 0;
                foreach ($fuels as $k => $fuel) {
                    $index = findIndexByLabel($dhead2, 'Montant Stock de Sécurité');
                    abort_if(is_null($index), 422, "Can't process: label \"Montant Stock de Sécurité\" not found in greatebook for fuel -> $fuel");

                    $t = 0;
                    foreach ($drows2 as $r) {
                        $v = (float) @$r[$index]['vv'];
                        $zo = @$r[4]['v'];
                        $pro = @$r[5]['v'];
                        abort_if(!in_array($zo, mainWays()), 422, "Can't process: Invalid zone : $zo");
                        abort_if(!in_array($pro, mainfuels()), 422, "Can't process: Invalid product : $pro");
                        if ($pro === $fuel && in_array($zo, $zones)) {
                            $t += round($v, 3); //
                        }
                    }
                    $tot += $t;
                    $line0[] = [
                        'label' => v($t),
                        'title' => "Montant Stock de Sécurité du produit $fuel",
                        'tag' => 'stock_non_reverse',
                        'value' => $fuel,
                        'href' => gb_href(['item' => 'cc', 'date1' => request('date1'), 'date2' => request('date2'), 'fuel' => $fuel]),
                        'title' => "Afficher les détails pour le produit $fuel.",
                    ];
                }
                $line0[] = [
                    'label' => v($tot),
                    'title' => "Total Montant Stock de Sécurité des produits.",
                    'tag' => 'stock_non_reverse',
                    'value' => "total",
                    'href' => gb_href(['item' => 'cc', 'date1' => request('date1'), 'date2' => request('date2')]),
                ];
                $rows[] = $line0;

                //////////////////////

                $line0 = [];
                $line0[] = [
                    'label' => "STOCK DE SÉCURITÉ COLLECTÉ REVERSÉ",
                    // 'class' => 'title2',
                ];
                foreach ($fuels as $fuel) {
                    $line0[] = [
                        'label' => "",
                        'tag' => 'stock_reverse',
                        'value' => $fuel,
                    ];
                }
                $line0[] = [
                    'label' => "",
                    'tag' => 'stock_reverse',
                    'value' => "total",
                ];
                $rows[] = $line0;
                ///////////////////////

                $line0 = [];
                $line0[] = [
                    'label' => "TOTAL CREANCES DE L'ETAT SUR LA SOCIETE",
                    'class' => 'title1',
                ];
                foreach ($fuels as $fuel) {
                    $line0[] = [
                        'label' => "",
                        'class' => 'title1',
                        'tag' => 'total_creance_etat',
                        'value' => $fuel,
                        'title' => "St. Séc Collecté non versé + St. Séc Collecté versé ($fuel)"
                    ];
                }
                $line0[] = [
                    'label' => "",
                    'class' => 'title1',
                    'tag' => 'total_creance_etat',
                    'value' => 'total',
                    'title' => 'Total Créance'
                ];
                $rows[] = $line0;
                ///////////////////////

                $line0 = [];
                $line0[] = [
                    'label' => "SOLDE CROISEMENT",
                    'class' => 'title1',
                ];
                foreach ($fuels as $fuel) {
                    $line0[] = [
                        'label' => "",
                        'class' => 'title1',
                        'tag' => 'solde_croisement',
                        'value' => $fuel,
                        'title' => "Total créance de la société sur l'état - Total créance de l'état sur la société ($fuel)"
                    ];
                }
                $line0[] = [
                    'label' => "",
                    'class' => 'title1',
                    'tag' => 'solde_croisement',
                    'value' => 'total',
                    'title' => 'Total croisement'
                ];
                $rows[] = $line0;
                ///////////////////////

                array_unshift($rows, $head);

                return response()->json(['rows' => $rows, 'errors' => $data['errors']]);
            }

            if ($type === 'balancefisc') {
                $data = $this->greatBookFiscData();
                $dhead = $data['head'];
                $drows = $data['rows'];

                $zones = (array) request('zone');
                $fuels = (array) request('fuel');

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

                $rows = [];

                /// PARA FISC
                $items1 = [
                    'Stock de Sécurité 1',
                    'Stock de Sécurité 2',
                    'Effort de reconstruction et Stock Stratégiques',
                    "FONER (Fonds National d'Entretien Routier)",
                    'Marquage moléculaire',
                    'Interventions Economiques',
                    'CRP & Comité de suivi des Prix des produits Petroliers'
                ];

                $tabt = [];
                foreach ($fuels as $z) {
                    $tabv["tot_para_$z"] = 0;
                    $tabt["tot_fisc_$z"] = 0;
                }

                foreach ($items1 as $ti) {
                    $line0 = [];
                    $line0[] = [
                        'label' => $ti,
                        // 'href' => route('provider.accounting', ['item' => 'cc', 'date1' => request('date1'), 'date2' => request('date2')]),
                        // 'title' => "Afficher les détails de tous les produits.",
                    ];

                    $tot = 0;
                    foreach ($fuels as $fuel) {
                        $t = 0;
                        $index = findIndexByLabel($dhead, $ti);
                        abort_if(is_null($index), 422, "Can't process: label \"$ti\" not found in greatebook");

                        //
                        foreach ($drows as $r) {
                            $v = (float) @$r[$index + 1]['vv']; // colonne suivante qui represente le montant
                            $zo = @$r[4]['v'];
                            $pro = @$r[5]['v'];
                            abort_if(!in_array($zo, mainWays()), 422, "Can't process: Invalid zone : $zo");
                            abort_if(!in_array($pro, mainfuels()), 422, "Can't process: Invalid product : $pro");
                            if ($pro === $fuel && in_array($zo, $zones)) {
                                $t += round($v, 3); //
                            }
                        }

                        incr($tabv, "tot_para_$fuel", $t);

                        $tot += $t;
                        $line0[] = [
                            'label' => v($t),
                            'title' => "Montant $ti du produit $fuel",
                            // 'href' => route('provider.accounting', ['item' => 'cc', 'date1' => request('date1'), 'date2' => request('date2'), 'fuel' => $fuel]),
                            // 'title' => "Afficher les détails pour le produit $fuel.",
                        ];
                    }
                    $line0[] = [
                        'label' => v($tot),
                        'title' => "Total Montant $ti des produits.",
                        // 'href' => route('provider.accounting', ['item' => 'cc', 'date1' => request('date1'), 'date2' => request('date2')]),
                    ];
                    $rows[] = $line0;
                }

                $line0 = [];
                $line0[] = [
                    'label' => "TOTAL PARA FISCALITE",
                    'class' => "title1",
                    'href' => gb_href(['item' => 'pf', 'el' => 'item1', 'date1' => request('date1'), 'date2' => request('date2')]),
                    'title' => "Afficher les détails para fiscalité.",
                ];
                $tot = 0;
                foreach ($fuels as $fuel) {
                    $v = $tabv["tot_para_$fuel"];
                    $tot += $v;
                    $line0[] = [
                        'label' => v($v),
                        'class' => "title1",
                        'href' => gb_href(['item' => 'pf', 'el' => 'item1', 'fuel' => $fuel, 'date1' => request('date1'), 'date2' => request('date2')]),
                        'title' => "Afficher les détails para fiscalité du produit $fuel.",
                    ];
                }
                $line0[] = [
                    'label' => v($tot),
                    'class' => "title1",
                    'href' => gb_href(['item' => 'pf', 'el' => 'item1', 'date1' => request('date1'), 'date2' => request('date2')]),
                    'title' => "Afficher les détails para fiscalité.",
                ];
                $rows[] = $line0;


                /// FISC
                $items1 = [
                    'TVA à la vente (TVAV)',
                    'Droits de douane (10% PMF Commercial)',
                    'Droits de consommation (25%, 15%, 0% du PMFF)',
                    "Droits de consommation (25%, 15%, 0% du PMFF)",
                    "TVA à l'importation (TVAI) = 16%(PMFC+DD+DC)",
                    "TVA à l'importation (TVAI) = 16%(PMFC+DD+DC)",
                    "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)"
                ];

                $tabt = [];
                foreach ($fuels as $z) {
                    $tabv["tot_para_$z"] = 0;
                    $tabv["tot_fisc_$z"] = 0;
                }

                foreach ($items1 as $ti) {
                    $line0 = [];
                    $line0[] = [
                        'label' => $ti,
                        // 'href' => gb_href( ['item' => 'cc', 'date1' => request('date1'), 'date2' => request('date2')]),
                        // 'title' => "Afficher les détails de tous les produits.",
                    ];

                    $tot = 0;
                    foreach ($fuels as $fuel) {
                        $t = 0;
                        $index = findIndexByLabel($dhead, $ti);
                        abort_if(is_null($index), 422, "Can't process: label \"$ti\" not found in greatebook");

                        //
                        foreach ($drows as $r) {
                            $v = (float) @$r[$index + 1]['vv']; // colonne suivante qui represente le montant
                            $zo = @$r[4]['v'];
                            $pro = @$r[5]['v'];
                            abort_if(!in_array($zo, mainWays()), 422, "Can't process: Invalid zone : $zo");
                            abort_if(!in_array($pro, mainfuels()), 422, "Can't process: Invalid product : $pro");
                            if ($pro === $fuel && in_array($zo, $zones)) {
                                $t += round($v, 3); //
                            }
                        }

                        incr($tabv, "tot_fisc_$fuel", $t);

                        $tot += $t;
                        $line0[] = [
                            'label' => v($t),
                            'title' => "Montant $ti du produit $fuel",
                            // 'href' => gb_href( ['item' => 'cc', 'date1' => request('date1'), 'date2' => request('date2'), 'fuel' => $fuel]),
                            // 'title' => "Afficher les détails pour le produit $fuel.",
                        ];
                    }
                    $line0[] = [
                        'label' => v($tot),
                        'title' => "Total Montant $ti des produits.",
                        // 'href' => gb_href( ['item' => 'cc', 'date1' => request('date1'), 'date2' => request('date2')]),
                    ];
                    $rows[] = $line0;
                }

                $line0 = [];
                $line0[] = [
                    'label' => "TOTAL FISCALITE",
                    'class' => "title1",
                    'href' => gb_href(['item' => 'pf', 'el' => 'item2', 'date1' => request('date1'), 'date2' => request('date2')]),
                    'title' => "Afficher les détails fiscalité.",
                ];
                $tot = 0;
                foreach ($fuels as $fuel) {
                    $v = $tabv["tot_fisc_$fuel"];
                    $tot += $v;
                    $line0[] = [
                        'label' => v($v),
                        'class' => "title1",
                        'href' => gb_href(['item' => 'pf', 'el' => 'item2', 'fuel' => $fuel, 'date1' => request('date1'), 'date2' => request('date2')]),
                        'title' => "Afficher les détails fiscalité du produit $fuel.",
                    ];
                }
                $line0[] = [
                    'label' => v($tot),
                    'class' => "title1",
                    'href' => gb_href(['item' => 'pf', 'el' => 'item2', 'date1' => request('date1'), 'date2' => request('date2')]),
                    'title' => "Afficher les détails fiscalité.",
                ];
                $rows[] = $line0;

                array_unshift($rows, $head);

                return response()->json(['rows' => $rows, 'errors' => $data['errors']]);
            }

            if ($type === 'balancelog') {
                $data = $this->greatBookLogData();
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

                $title = itemslog();

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
                            abort_if(is_null($index), 422, "Can't process: label \"$ti->label\" not found in greatebook");

                            foreach ($drows as $r) {
                                $v = (float) @$r[$index]['vv'];
                                $zo = $r[4]['v'];
                                $pro = $r[5]['v'];
                                abort_if(!in_array($zo, mainWays()), 422, "Can't process: Invalid zone : $zo");
                                abort_if(!in_array($pro, mainfuels()), 422, "Can't process: Invalid product : $pro");
                                if ($pro === $fuel && $zone === $zo) {
                                    $tot += round($v, 3); //
                                }
                            }
                            $line[] = ['label' => v($tot)];
                            $tot2  += $tot;
                            $v0 = (float) @$tabv["v_$zone"];
                            $tabv["v_$zone"] =  $v0 + $tot;
                        }
                        $line[] = ['label' => v($tot2)];
                        $rows[] = $line;
                    }

                    $line0 = [];
                    $line0[] = [
                        'label' => $ti->label,
                        'class' => 'title1',
                        'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val]),
                        'title' => "Afficher les valeurs $ti->label de toutes les zones",
                    ];
                    $t0 = 0;

                    foreach ($tabv as $k => $v) {
                        $z = array_values(array_filter(explode('v_', $k)))[0];
                        $line0[] = [
                            'label' => v($v),
                            'class' => 'title1',
                            'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val, 'z' => $z]),
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
                        'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'el' => $ti->val]),
                        'title' => "Afficher les valeurs $ti->label de toutes les zones",
                    ];
                    $rows[] = $line0;
                }

                $line0 = [];
                $line0[] = [
                    'label' => "TOTAL GENERAL",
                    'class' => 'title1',
                    'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2')]),
                    'title' => 'Afficher les détails pour toutes les zones'
                ];
                $t0 = 0;
                foreach ($tabt as $k => $v) {
                    $z = array_values(array_filter(explode('t_', $k)))[0];
                    $line0[] = [
                        'label' => v($v),
                        'class' => 'title1',
                        'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2'), 'z' => $z]),
                        'title' => "Afficher le Total de la zone $z",
                    ];
                    $t0 += $v;
                }

                $line0[] = [
                    'label' => v($t0),
                    'class' => 'title1',
                    'href' => gb_href(['item' => 'gb', 'date1' => request('date1'), 'date2' => request('date2')]),
                    'title' => 'Afficher les détails pour toutes les zones'
                ];
                $rows[] = $line0;
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
        $isState = false;
        $user = auth()->user();
        if ($user->user_role == 'petrolier') {
            $entity = $user->entities()->first();
        } else if ($user->user_role == 'etatique') {
            $entity  = Entity::findOrFail(request('entity_id'));
            $isState = true;
        } else {
            abort(403);
        }

        $from = request('date1') ?? nnow()->toDateString();
        $to = request('date2') ?? nnow()->toDateString();

        $head = [
            ['label' => 'ID'],
            ['label' => 'Terminal'],
            ['label' => 'Date'],
            ['label' => 'Localité'],
            ['label' => 'Voie'],
            ['label' => 'Produit'],
            ['label' => 'Bon de livraison'],
            ['label' => 'Progr. de livraison'],
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

        $sales = $entity->sales()->where('from_state', from_state())->where(function ($q) use ($reqfuel, $reqzone) {
            $q->whereIn('product', $reqfuel);
            $q->whereIn('way', $reqzone);
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
            $zoneObj = Zone::where(compact('zone'))->first();

            $startOfMonth = $saledate->copy()->startOfMonth();
            $endOfMonth   = $saledate->copy()->endOfMonth();

            if ($isState) {
                $pmfc_reel = (float) AverageFuelPrice::whereYear('month', $saledate->year)
                    ->whereMonth('month', $saledate->month)
                    ->where('product', $fuel)
                    ->where('zone_id', $zoneObj->id)
                    ->first()?->avg_price;
                $pmlab = "Prix moyen d'achat $fuel (zone $zone) du {$startOfMonth->format('d-m-Y')} au {$endOfMonth->format('d-m-Y')}";
                if (!$pmfc_reel) {
                    $errors[] = "Aucun prix d'achat moyen n'a été trouvé pour la vente #$e->id du {$e->date?->format('d-m-Y')} ($fuel, $zone)";
                }
            } else {
                $pmfc_reel = (float) ($entity->purchases()->where('from_state', 0)->where(function ($q) use ($fuel) {
                    $q->where('product', $fuel);
                })->where('way', $zone)->whereBetween('date', [$startOfMonth, $endOfMonth])->avg('unitprice') ?? 0);
                $pmlab = "Moyenne mensuelle du prix unitaire d'achat $fuel (zone $zone) du {$startOfMonth->format('d-m-Y')} au {$endOfMonth->format('d-m-Y')}";
            }

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
                ['v' => v($pmfc_reel), 'class' => 'bigtitle', 'title' => $pmlab],
                ['v' => v($pmfc_struct)],
                ['v' => v($ecart_pmf), 'class' => 'bigtitle', 'title' => "PMFC REEL - PMFC STRUCTURE"],
                ['v' => v($pmag_pmfc_socom), 'vv' => $pmag_pmfc_socom,  'class' => 'bigtitle', 'title' => "ECART PMFC * M3"],
                ['v' => v($pmag_marge_socom), 'vv' => $pmag_marge_socom, 'class' => 'bigtitle', 'title' => '10% de PMAG PMFC SOCOM'],
                ['v' => v($tx_reel)],
                ['v' => v($tx_str)],
                ['v' => v($variation_change), 'class' => 'bigtitle', 'title' => '(Taux Réel - Taux Structure)/Taux Réel'],
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
        $isState = false;
        if ($user->user_role == 'petrolier') {
            $entity = $user->entities()->first();
        } else if ($user->user_role == 'etatique') {
            $entity  = Entity::findOrFail(request('entity_id'));
            $isState = true;
        } else {
            abort(403);
        }

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
            ['label' => 'Progr. de livraison'],
            ['label' => 'Client'],
            ['label' => 'Lata'],
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

        $sales = $entity->sales()->where('from_state', from_state())->where(function ($q) use ($reqfuel, $reqzone) {
            $q->whereIn('product', $reqfuel);
            $q->whereIn('way', $reqzone);
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
            $zoneObj = Zone::where(compact('zone'))->first();

            $startOfMonth = $saledate->copy()->startOfMonth();
            $endOfMonth   = $saledate->copy()->endOfMonth();

            if ($isState) { // en mode view, etatique ici pmfc_reel uhm uhm  uhm
                $pmfc_reel = (float) AverageFuelPrice::whereYear('month', $saledate->year)
                    ->whereMonth('month', $saledate->month)
                    ->where('product', $fuel)
                    ->where('zone_id', $zoneObj->id)
                    ->first()?->avg_price;
            } else {
                $pmfc_reel = (float) ($entity->purchases()->where('from_state', 0)->where(function ($q) use ($fuel) {
                    $q->where('product', $fuel);
                })->where('way', $zone)->whereBetween('date', [$startOfMonth, $endOfMonth])->avg('unitprice') ?? 0);
            }

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
                ['v' => v($mss), 'vv' => $mss, 'class' => 'bigtitle', 'title' => "(Stock de Sécurité 1 + Stock de Sécurité 2) * M3 $fuel (zone $zone)"],
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

    private function greatBookFiscData()
    {
        $reqzone = (array) request('zone');
        $reqfuel = (array) request('fuel');
        $items = request('items');

        $user = auth()->user();
        if ($user->user_role == 'petrolier') {
            $entity = $user->entities()->first();
        } else if ($user->user_role == 'etatique') {
            $entity  = Entity::findOrFail(request('entity_id'));
        } else {
            abort(403);
        }

        $from = request('date1') ?? nnow()->toDateString();
        $to = request('date2') ?? nnow()->toDateString();

        $head = [
            ['label' => 'ID'],
            ['label' => 'Terminal'],
            ['label' => 'Date'],
            ['label' => 'Localité'],
            ['label' => 'Voie'],
            ['label' => 'Produit'],
            ['label' => 'Bon de livraison'],
            ['label' => 'Progr. de livraison'],
            ['label' => 'Client'],
            ['label' => 'Lata'],
            ['label' => 'L15'],
            ['label' => 'Densité'],
            ['label' => 'M3'],
        ];

        $rows = [];
        $errors = [];

        $labels = [
            ['label' => 'Stock de Sécurité 1'],
            ['label' => 'Montant Sto. Sécurité 1'],
            ['label' => 'Stock de Sécurité 2'],
            ['label' => 'Montant Sto. Sécurité 2'],
            ['label' => 'Stock de Sécurité'],
            ['label' => 'Montant Stock de Sécurité'],
            ['label' => 'Effort de reconstruction et Stock Stratégiques'],
            ['label' => 'Montant Eff. reconst. et Sto. Strat.'],
            ['label' => "FONER (Fonds National d'Entretien Routier)"],
            ['label' => 'Montant FONER'],
            ['label' => 'Marquage moléculaire'],
            ['label' => 'Montant Marq. molécul.'],
            ['label' => 'Interventions Economiques'],
            ['label' => 'Montant Interv. Eco.'],
            ['label' => 'CRP & Comité de suivi des Prix des produits Petroliers'],
            ['label' => 'Montant CRP & Comité ...'],
            ['label' => 'TVA à la vente (TVAV)'],
            ['label' => 'Montant TVAV'],
            ['label' => 'Droits de douane (10% PMF Commercial)'],
            ['label' => 'Montant Droits de douane'],
            ['label' => 'Droits de consommation (25%, 15%, 0% du PMFF)'],
            ['label' => 'Montant Droits de consommation'],
            ['label' => 'TVA à l\'importation (TVAI) = 16%(PMFC+DD+DC)'],
            ['label' => 'Montant TVAI'],
            ['label' => 'TVA nette à l\'intérieur (TVAIr=TVAV-TVAI)'],
            ['label' => 'Montant TVAIr'],
        ];

        $head = [...$head, ...$labels];

        $sales = $entity->sales()->where('from_state', from_state())->where(function ($q) use ($reqfuel, $reqzone) {
            $q->whereIn('product', $reqfuel);
            $q->whereIn('way', $reqzone);
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

            $ss_1 = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', 'Stock de Sécurité 1'))->first()?->amount;
            $mss1 = $ss_1 * $m3;

            $ss_2 = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', 'Stock de Sécurité 2'))->first()?->amount;
            $mss2 = $ss_2 * $m3;

            $ss = $ss_1 + $ss_2;
            $mss = $ss * $m3;

            $effort_reconst = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', 'Effort de reconstruction et Stock Stratégiques'))->first()?->amount;
            $mt_effort_reconst = $effort_reconst * $m3;

            $foner = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', "FONER (Fonds National d'Entretien Routier)"))->first()?->amount;
            $mt_foner = $foner * $m3;

            $marquage_molecu = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', "Marquage moléculaire"))->first()?->amount;
            $mt_marquage_molecu = $marquage_molecu * $m3;

            $intervention_eco = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', "Interventions Economiques"))->first()?->amount;
            $mt_intervention_eco = $intervention_eco * $m3;

            $cpr_comite_suivi = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', "CRP & Comité de suivi des Prix des produits Petroliers"))->first()?->amount;
            $mt_cpr_comite_suivi = $cpr_comite_suivi * $m3;

            $cpr_comite_suivi = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', "CRP & Comité de suivi des Prix des produits Petroliers"))->first()?->amount;
            $mt_cpr_comite_suivi = $cpr_comite_suivi * $m3;

            $tva_vente = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', "TVA à la vente (TVAV) pour calcul"))->first()?->amount;
            $mt_tva_vente = $tva_vente * $m3;

            $droit_douane = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', "Droits de douane (10% PMF Commercial)"))->first()?->amount;
            $mt_droit_douane = $droit_douane * $m3;


            $droit_consom = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', "Droits de consommation (25%, 15%, 0% du PMFF)"))->first()?->amount;
            $mt_droit_consom = $droit_consom * $m3;

            $tva_import = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', "TVA à l'importation (TVAI) = 16%(PMFC+DD+DC)"))->first()?->amount;
            $mt_tva_import = $tva_import * $m3;

            $tva_interieur = $tva_vente - $tva_import;
            $mt_tva_interieur = $tva_interieur * $m3;

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
                ['v' => v($ss_1), 'title' => "Stock de sécurité 1 $fuel (zone $zone) de la structure de prix #{$structure?->id} du {$structure?->from?->format('d-m-Y')}"],
                ['v' => v($mss1), 'vv' => $mss1, 'title' => "Stock de sécurité 1 * M3"],
                ['v' => v($ss_2), 'title' => "Stock de sécurité 2 $fuel (zone $zone) de la structure de prix #{$structure?->id} du {$structure?->from?->format('d-m-Y')}"],
                ['v' => v($mss2), 'vv' => $mss2, 'title' => "Stock de sécurité 2 * M3"],
                ['v' => v($ss)],
                ['v' => v($mss)],
                ['v' => v($effort_reconst)],
                ['v' => v($mt_effort_reconst), 'vv' => $mt_effort_reconst, 'title' => "Effort Reconst. St. Strat. * M3"],
                ['v' => v($foner)],
                ['v' => v($mt_foner), 'vv' => $mt_foner, 'title' => "FONER * M3"],
                ['v' => v($marquage_molecu)],
                ['v' => v($mt_marquage_molecu), 'vv' => $mt_marquage_molecu, 'title' => "Marquage Molécule * M3"],
                ['v' => v($intervention_eco)],
                ['v' => v($mt_intervention_eco), 'vv' => $mt_intervention_eco, 'title' => "Intervention Eco. * M3"],
                ['v' => v($cpr_comite_suivi)],
                ['v' => v($mt_cpr_comite_suivi), 'vv' => $mt_cpr_comite_suivi, 'title' => "CRP * M3"],
                ['v' => v($tva_vente)],
                ['v' => v($mt_tva_vente), 'vv' => $mt_tva_vente, 'title' => "TVAV * M3"],
                ['v' => v($droit_douane)],
                ['v' => v($mt_droit_douane), 'vv' => $mt_droit_douane, 'title' => "DD * M3"],
                ['v' => v($droit_consom)],
                ['v' => v($mt_droit_consom), 'vv' => $mt_droit_consom, 'title' => "DC * M3"],
                ['v' => v($tva_import)],
                ['v' => v($mt_tva_import), 'vv' => $mt_tva_import, 'title' => "TVAI * M3"],
                ['v' => v($tva_interieur)],
                ['v' => v($mt_tva_interieur), 'vv' => $mt_tva_interieur, 'title' => "TVAIr * M3"],
            ];

            $rows[] = $pline;
        }

        $errors = array_values(array_unique($errors));

        if ($items == 'item1') {
            $head = array_slice($head, 0, 29);
            $t = [];
            foreach ($rows as $r) {
                $t[] = array_slice($r, 0, 29);
            }
            $rows = $t;
        }

        if ($items == 'item2') {
            $head0 = array_slice($head, 0, 13);
            $head1 = array_slice($head, 29);
            $head  = [...$head0, ...$head1];

            $t = [];
            foreach ($rows as $r) {
                $head0 = array_slice($r, 0, 13);
                $head1 = array_slice($r, 29);
                $t[]   = [...$head0, ...$head1];
            }
            $rows = $t;
        }

        return compact('head', 'rows', 'errors');
    }

    private function greatBookLogData()
    {
        $reqzone = (array) request('zone');
        $reqfuel = (array) request('fuel');
        $items = request('items');

        $user = auth()->user();
        if ($user->user_role == 'logisticien') {
            $entity = $user->entities()->first();
        } else if ($user->user_role == 'etatique') {
            $entity  = Entity::findOrFail(request('entity_id'));
        } else {
            abort(403);
        }

        $from = request('date1') ?? nnow()->toDateString();
        $to = request('date2') ?? nnow()->toDateString();

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
            ['label' => 'TAUX REEL'],
            ['label' => 'TAUX STRUCTURE'],
            ['label' => 'Variation Change(%)'],
            ['label' => 'CHARGES SOCIR'],
            ['label' => 'CHARGES SEP CONGO'],
            ['label' => 'CHARGES SPSA-COBIL'],
            ['label' => 'CHARGES LEREXCOM', 'class' => "bigtitle"],
            ['label' => 'PMAG CHANGE SOCIR', 'class' => "bigtitle"],
            ['label' => 'PMAG CHANGE SEP CONGO'],
            ['label' => 'PMAG CHANGE SPSA-COBIL'],
            ['label' => 'PMAG CHANGE LEREXCOM '],
        ];

        $head = [...$head, ...$labels];

        $sales = $entity->sales()->where('from_state', from_state())->where(function ($q) use ($reqfuel, $reqzone) {
            $q->whereIn('product', $reqfuel);
            $q->whereIn('way', $reqzone);
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

            $charge_socir = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', 'Charges SOCIR'))->first()?->amount;

            if (!$charge_socir) {
                $errors[] = "Aucune valeur Charge SOCIR n'a été trouvée dans la strucutre de prix #$structure?->id $structure?->name du {$structure?->from?->format('d-m-Y')} ($fuel, $zone)";
            }

            $charge_sep_congo = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', 'Charges Sep Congo'))->first()?->amount;

            if (!$charge_sep_congo) {
                $errors[] = "Aucune valeur Charge Sep Congo n'a été trouvée dans la strucutre de prix #$structure?->id $structure?->name du {$structure?->from?->format('d-m-Y')} ($fuel, $zone)";
            }

            $charge_spa_cobil = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', 'Charges SPSA-COBIL'))->first()?->amount;

            if (!$charge_spa_cobil) {
                $errors[] = "Aucune valeur Charge SPSA-COBIL n'a été trouvée dans la strucutre de prix #$structure?->id $structure?->name du {$structure?->from?->format('d-m-Y')} ($fuel, $zone)";
            }

            $charge_lerexcom = (float)@$structure?->fuelprices()
                ->whereHas('zone', fn($q) => $q->where('zone', $zone))
                ->whereHas('fuel', fn($q) => $q->where('fuel', $fuel))
                ->whereHas('label', fn($q) => $q->where('label', 'Charges LEREXCOM PETROLEUM ET Appui Terrestre'))->first()?->amount;

            if (!$charge_lerexcom) {
                $errors[] = "Aucune valeur Lerexcom n'a été trouvée dans la strucutre de prix #$structure?->id $structure?->name du {$structure?->from?->format('d-m-Y')} ($fuel, $zone)";
            }

            $pmag_change_socir = $charge_socir * $variation_change * $m3;
            $pmag_change_sep_congo = $charge_sep_congo * $variation_change * $m3;
            $pmag_change_cobil = $charge_spa_cobil * $variation_change * $m3;
            $pmag_change_lerexcom = $charge_lerexcom * $variation_change * $m3;

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
                ['v' => v($tx_reel)],
                ['v' => v($tx_str)],
                ['v' => v($variation_change), 'class' => 'bigtitle', 'title' => '(Taux Réel - Taux Structure)/Taux Réel'],
                ['v' => v($charge_socir)],
                ['v' => v($charge_sep_congo)],
                ['v' => v($charge_spa_cobil)],
                ['v' => v($charge_lerexcom)],
                ['v' => v($pmag_change_socir), 'vv' => $pmag_change_socir, 'class' => 'bigtitle', 'title' => 'Charges SOCIR * Variation change * M3'],
                ['v' => v($pmag_change_sep_congo), 'vv' => $pmag_change_sep_congo, 'class' => 'bigtitle', 'title' => 'Charges Sep Congo * Variation change * M3'],
                ['v' => v($pmag_change_cobil), 'vv' => $pmag_change_cobil, 'class' => 'bigtitle', 'title' => 'Charges SPSA-COBIL * Variation change * M3'],
                ['v' => v($pmag_change_lerexcom), 'vv' => $pmag_change_lerexcom,  'class' => 'bigtitle', 'title' => 'Charges LEREXCOM * Variation change * M3'],
            ];

            $rows[] = $pline;
        }

        $errors = array_values(array_unique($errors));

        $indexes = null;

        if ($user->user_role === 'logisticien') {
            $name = $user->entities()->first()?->shortname;
            if ($name == 'SEP CONGO') {
                $indexes = array_merge(range(0, 15), [17, 21]);
            }
            if ($name == 'LEREXCOM') {
                $indexes = array_merge(range(0, 15), [19, 23]);
            }
            if ($name == 'SPSA') {
                $indexes = array_merge(range(0, 15), [18, 22]);
            }
            if ($name == 'SOCIR') {
                $indexes = array_merge(range(0, 15), [16, 20]);
            }
        } else if ($user->user_role == 'etatique') {
            if ($items == 'item1') {
                // Colonnes 0 à 20
                $indexes = range(0, 20);
            }

            if ($items == 'item2') {
                // Colonnes 0 à 19 + 21
                $indexes = array_merge(range(0, 19), [21]);
            }

            if ($items == 'item3') {
                // Colonnes 0 à 19 + 22
                $indexes = array_merge(range(0, 19), [22]);
            }

            if ($items == 'item4') {
                // Colonnes 0 à 19 + 23
                $indexes = array_merge(range(0, 19), [23]);
            }
        }

        if ($indexes !== null) {
            // On filtre le head
            $head = array_values(array_intersect_key($head, array_flip($indexes)));

            // On filtre chaque row
            $t = [];
            foreach ($rows as $r) {
                $t[] = array_values(array_intersect_key($r, array_flip($indexes)));
            }
            $rows = $t;
        }

        return compact('head', 'rows', 'errors');
    }
}
