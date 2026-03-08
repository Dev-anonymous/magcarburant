<?php

use App\Models\AverageFuelPrice;
use App\Models\Entity;
use App\Models\Fuel;
use App\Models\Fuelprice;
use App\Models\Label;
use App\Models\StateFuelprice;
use App\Models\StateStructureprice;
use App\Models\Structureprice;
use App\Models\User;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function nnow()
{
    return now('Africa/Lubumbashi');
}

function noteditable($type, $zone)
{
    $type = strtolower($type);
    $zone = strtolower($zone);

    $noedit = [
        'terrestre' => [
            'nord' => [
                'Total frais des sociétés de logistique',
                'Total frais des sociétés Commerciales',
                'Total Parafiscalité',
                'Total Fiscalité 1',
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                "Total Fiscalité 2",
                "Prix de référence réel (USD/M3)",
                "Prix de référence à appliquer (USD/L)"
            ],
            'sud' => [
                "Total frais des sociétés de logistique",
                "Total frais des sociétés Commerciales",
                "Total Parafiscalité",
                "Total Fiscalité 1",
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                "Total Fiscalité 2",
                "Prix de référence réel (USD/M3)",
                "Prix de référence à appliquer (USD/L)",
            ],
            'est' => [
                "Total frais des sociétés de logistique",
                "Total frais des sociétés Commerciales",
                "Total Parafiscalité",
                "Total Fiscalité 1",
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                "Total Fiscalité 2",
                "Prix de référence réel (USD/M3)",
                "Prix de référence à appliquer (USD/L)",
            ],
            'ouest' => [
                "Total frais des sociétés de logistique",
                "Total frais des sociétés Commerciales",
                "Total Parafiscalité",
                "Total Fiscalité 1",
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                "Total Fiscalité 2",
                "Prix de référence réel (USD/M3)",
                "Prix de référence à appliquer (USD/L)",
            ],
        ],
        'aviation' => [
            'nord' => [],
            'sud' => [
                "Total frais des sociétés de logistique",
                "Total frais des sociétés Commerciales",
                'Total Fiscalité 1',
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                "Total Fiscalité 2",
                "Prix de référence réel (USD/M3)",
                "Prix de référence à appliquer (USD/L)"
            ],
            'est' => [
                "Total frais des sociétés de logistique",
                "Total frais des sociétés Commerciales",
                'Total Fiscalité 1',
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                "Total Fiscalité 2",
                "Prix de référence réel (USD/M3)",
                "Prix de référence à appliquer (USD/L)"
            ],
            'ouest' => [
                "Total frais des sociétés de logistique",
                "Total frais des sociétés Commerciales",
                'Total Fiscalité 1',
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                "Total Fiscalité 2",
                "Prix de référence réel (USD/M3)",
                "Prix de référence à appliquer (USD/L)"
            ],
        ]
    ];

    return (array) @$noedit[$type][$zone];
}

function uneditable()
{
    $ignore = [];
    foreach (['aviation', 'terrestre'] as $t) {
        foreach (mainWays() as $z) {
            $ignore = [...$ignore, ...noteditable($t, $z)];
        }
    }
    return array_values(array_unique($ignore));
}

function mainfuels()
{
    return ['ESSENCE', 'PETROLE', 'GASOIL', 'FOMI', 'JET'];
}

function mainWays()
{
    return ['NORD', 'SUD', 'EST', 'OUEST'];
}

function initfuelprice(Structureprice|StateStructureprice $structure)
{
    $isState = $structure instanceof StateStructureprice;

    if ($isState) {
        $exists = StateFuelprice::where(['state_structureprice_id' => $structure->id])
            ->exists();
    } else {
        $exists = FuelPrice::where(['structureprice_id' => $structure->id])
            ->exists();
    }
    if ($exists) return;


    $zoneLabels = [
        'Nord' => [
            'terrestre' => [
                'Platts',
                'Premium/TM',
                'PMFC en TM',
                'Densité',
                'PMFC Ouest',
                "Différentiel de livraison à l'intérieur",
                'PMFC en M3',
                'Charges Sep Congo et Autres entrepots',
                'Total frais des sociétés de logistique',
                "Charges d'exploitation Sociétés commerciales",
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'Stock de sécurité 1',
                'Stock de sécurité 2',
                'Marquage moléculaire',
                "FONER (Fonds National d'Entretien Routier)",
                'Total Parafiscalité',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV) pour calcul',
                'Droits de douane (10% PMF Commercial)',
                'Droits de consommation (25%, 15%, 0% du PMFF)',
                "TVA à l'importation (TVAI) = 16%(PMFC+DD+DC)",
                'Total Fiscalité 1',
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)'

            ]
        ],
        'Sud' => [
            'terrestre' => [
                'PMFC en M3',
                "Charges d'exploitation logisticiens (frais d'entreprot)",
                'Total frais des sociétés de logistique',
                "Charges d'exploitation Sociétés commerciales",
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'Stock de sécurité 1',
                'Stock de sécurité 2',
                'Marquage moléculaire',
                "FONER (Fonds National d'Entretien Routier)",
                'Effort de reconstruction et Stock Stratégiques',
                'Interventions Economiques',
                'Total Parafiscalité',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV) pour calcul',
                'Droits de douane (10% PMF Commercial)',
                'Droits de consommation (25%, 15%, 0% du PMFF)',
                "TVA à l'importation (TVAI) = 16%(PMFC+DD+DC)",
                'Total Fiscalité 1',
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)',
            ],
            'aviation' => [
                'PMFC en M3',
                "Charges d'exploitation logisticien (Frais d'entrepot)",
                'Total frais des sociétés de logistique',
                "Charges d'exploitation Sociétés commerciales",
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV) pour calcul',
                'Droits de douane (10% PMF Commercial)',
                'Droits de consommation (25%, 15%, 0% du PMFF)',
                "TVA à l'importation (TVAI) = 16%(PMFC+DD+DC)",
                'Total Fiscalité 1',
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)',
            ]
        ],
        'Est' => [
            'terrestre' => [
                'PMFC en M3',
                "Charges d'exploitation logisticiens (frais d'entreprot)",
                'Capacités additionnelles KPS',
                'Total frais des sociétés de logistique',
                "Charges d'exploitation Sociétés commerciales",
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'Stock de sécurité 1',
                'Stock de sécurité 2',
                'Marquage moléculaire',
                "FONER (Fonds National d'Entretien Routier)",
                'Effort de reconstruction et Stock Stratégiques',
                'Interventions Economiques',
                'Total Parafiscalité',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV) pour calcul',
                'Droits de douane (10% PMF Commercial)',
                'Droits de consommation (25%, 15%, 0% du PMFF)',
                "TVA à l'importation (TVAI) = 16%(PMFC+DD+DC)",
                'Total Fiscalité 1',
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)',
            ],
            'aviation' => [
                'PMFC en M3',
                "Charges d'exploitation logisticien (Frais d'entrepot)",
                'Total frais des sociétés de logistique',
                "Charges d'exploitation Sociétés commerciales",
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV) pour calcul',
                'Droits de douane (10% PMF Commercial)',
                'Droits de consommation (25%, 15%, 0% du PMFF)',
                "TVA à l'importation (TVAI) = 16%(PMFC+DD+DC)",
                'Total Fiscalité 1',
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)',
            ]
        ],
        'Ouest' => [
            'terrestre' => [
                'Platts',
                'Premium/TM',
                'PMFC en TM',
                'Densité',
                'PMFC en M3',
                'Charges SOCIR',
                'Charges Sep Congo',
                'Charges SPSA-COBIL',
                'Charges LEREXCOM PETROLEUM ET Appui Terrestre',
                'Total frais des sociétés de logistique',
                "Charges d'exploitation Sociétés commerciales",
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'Stock de sécurité 1',
                'Stock de sécurité 2',
                'Effort de reconstruction et Stock Stratégiques',
                'CRP & Comité de suivi des Prix des produits Petroliers',
                'Marquage moléculaire',
                "FONER (Fonds National d'Entretien Routier)",
                'Interventions Economiques',
                'Total Parafiscalité',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV) pour calcul',
                'Droits de douane (10% PMF Commercial)',
                'Droits de consommation (25%, 15%, 0% du PMFF)',
                "TVA à l'importation (TVAI) = 16%(PMFC+DD+DC)",
                'Total Fiscalité 1',
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)',
            ],
            'aviation' => [
                'PMFC en M3',
                'Frais & Services SOCIR',
                "Charges d'exploitation Sep Congo",
                'Charges capacités additionnelles SPSA',
                'Total frais des sociétés de logistique',
                "Charges d'exploitation Sociétés commerciales",
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV) pour calcul',
                'Droits de douane (10% PMF Commercial)',
                'Droits de consommation (25%, 15%, 0% du PMFF)',
                "TVA à l'importation (TVAI) = 16%(PMFC+DD+DC)",
                'Total Fiscalité 1',
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)',
            ]
        ],
    ];

    foreach ($zoneLabels as $zoneName => $types) {
        $zone = Zone::where('zone', $zoneName)->first();
        foreach ($types as $fuelType => $labels) {
            $fuels = Fuel::where('fuel_type', $fuelType)->get();
            foreach ($fuels as $fuel) {
                foreach ($labels as $labelName) {
                    $label = Label::where('label', $labelName)->first();
                    if ($label && $zone) {
                        if ($isState) {
                            $exists = StateFuelprice::where('state_structureprice_id', $structure->id)
                                ->where('fuel_id', $fuel->id)
                                ->where('zone_id', $zone->id)
                                ->where('label_id', $label->id)
                                ->exists();
                        } else {
                            $exists = FuelPrice::where('structureprice_id', $structure->id)
                                ->where('fuel_id', $fuel->id)
                                ->where('zone_id', $zone->id)
                                ->where('label_id', $label->id)
                                ->exists();
                        }

                        if (! $exists) {
                            if ($isState) {
                                StateFuelprice::create([
                                    'state_structureprice_id' => $structure->id,
                                    'fuel_id' => $fuel->id,
                                    'zone_id' => $zone->id,
                                    'label_id' => $label->id,
                                    'amount' => 0,
                                    'currency' => 'USD',
                                ]);
                            } else {
                                FuelPrice::create([
                                    'structureprice_id' => $structure->id,
                                    'fuel_id' => $fuel->id,
                                    'zone_id' => $zone->id,
                                    'label_id' => $label->id,
                                    'amount' => 0,
                                    'currency' => 'USD',
                                ]);
                            }
                        }
                    }
                    // if (!$label) {
                    //     dd($labelName, $label, $zone->zone, $fuel->fuel);
                    // }
                }
            }
        }
    }
}

function userimg(?User $user = null)
{
    if (Auth::check()) {
        $user = $user ?? request()->user();
        $role = $user->user_role;
        $i = 'assets/images/avatar.png';
        if ($role === 'sudo') {
            return asset($i);
        }
        if (in_array($role, ['petrolier', 'logisticien', 'etatique'])) {
            $e = $user->entities()->first()?->logo;
            if ($e) {
                $i  = "storage/$e";
            }
            return asset($i);
        }
    }
}

function strname(?Entity $entity, Structureprice|StateStructureprice $str, $isState = false)
{
    if ($isState) {
        $n = StateStructureprice::whereNotNull('name')->count() + 1;
    } else {
        $n = $entity->structureprices()->whereNotNull('name')->count() + 1;
    }
    if ($n <= 9) {
        $n = "00$n";
    }
    if ($n >= 10 && $n < 100) {
        $n = "0$n";
    }
    $f = $str->from->format('ymd');
    $name = "STR-$n-$f";
    return $name;
}

function v($v, $decimal = 3)
{
    return number_format(round($v, $decimal), $decimal, ',', ' ');
}

function items()
{
    return  [
        (object)['label' => 'PMAG PMFC SOCOM', 'val' => 'item1'],
        (object)['label' => 'PMAG MARGE SOCOM', 'val' => 'item2'],
        (object)['label' => 'PMAG CHANGE SOCOM', 'val' => 'item3'],
    ];
}

function itemsCR()
{
    return  [
        (object)['label' => 'Stock de Sécurité 1', 'val' => 'item1'],
        (object)['label' => 'Stock de Sécurité 2', 'val' => 'item2'],
        (object)['label' => 'Stock de Sécurité', 'val' => 'item3'],
    ];
}

function itemsPara()
{
    return  [
        (object)['label' => 'Para fiscalité', 'val' => 'item1'],
        (object)['label' => 'Fiscalité', 'val' => 'item2'],
    ];
}

function itemslog()
{
    $user = request()->user();
    if ($user->user_role === 'logisticien') {
        $name = $user->entities()->first()?->shortname;
        if ($name == 'SEP CONGO') {
            return  [
                (object)['label' => 'PMAG CHANGE SEP CONGO', 'val' => 'item2'],
            ];
        }
        if ($name == 'LEREXCOM') {
            return  [
                (object)['label' => 'PMAG CHANGE LEREXCOM ', 'val' => 'item4'],
            ];
        }
        if ($name == 'SPSA') {
            return  [
                (object)['label' => 'PMAG CHANGE SPSA-COBIL', 'val' => 'item3'],
            ];
        }
        if ($name == 'SOCIR') {
            return  [
                (object)['label' => 'PMAG CHANGE SOCIR', 'val' => 'item1'],
            ];
        }
        return [];
    }

    return  [
        (object)['label' => 'PMAG CHANGE SOCIR', 'val' => 'item1'],
        (object)['label' => 'PMAG CHANGE SEP CONGO', 'val' => 'item2'],
        (object)['label' => 'PMAG CHANGE SPSA-COBIL', 'val' => 'item3'],
        (object)['label' => 'PMAG CHANGE LEREXCOM ', 'val' => 'item4'],
    ];
}

function findIndexByLabel(array $array, string $label): ?int
{
    foreach ($array as $index => $item) {
        if (isset($item['label']) && strtolower($item['label']) === strtolower($label)) {
            return $index;
        }
    }
    return null;
}


function userroles()
{
    return ['petrolier', 'logisticien', 'etatique'];
}

function incr(&$tab, $key,  $val)
{
    $v = (float)$tab[$key];
    $v += $val;
    $tab[$key] = $v;
}

function gb_href($params, $route = null)
{
    $user = request()->user();
    if ($user->user_role === 'petrolier') {
        $href = route($route ? "provider.$route" : 'provider.accounting', $params);
    } elseif ($user->user_role === 'logisticien') {
        $href = route('logistics.accounting', $params);
    } elseif ($user->user_role === 'etatique') {
        $mode = rmode();
        $entity  = Entity::findOrFail(request('entity_id'));
        $params = array_merge(['entity' => $entity->id, 'mode' => $mode], $params);
        $href = route($route ? "state.$route" : 'state.accounting', $params);
    } else {
        abort(422, "Invalid role !");
    }
    return $href;
}

function rmode()
{
    $mode = request()->route('mode', request()->header('x-page-mode', 'view'));
    return $mode;
}

function from_state()
{
    $user = request()->user();
    // mode view on charge les donnees selon $entity from_state => 0
    return $user->user_role === 'etatique' && rmode() === 'edit';
}


function state_route(string $name, $entity)
{
    $mode = request()->route('mode', rmode());

    $params = [
        'mode'   => $mode,
        'entity' => $entity,
    ];
    if (is_array($entity)) {
        unset($params['entity']);
        $params = [...$params, ...$entity];
    }
    return route("state.$name", $params);
}

function initAvgPrice()
{
    $year  = now()->year;
    $zones = Zone::all();
    foreach (mainfuels() as $product) {
        foreach ($zones as $zone) {
            for ($m = 1; $m <= 12; $m++) {
                $month = Carbon::create($year, $m, 1)->toDateString();
                AverageFuelPrice::firstOrCreate(
                    [
                        'product' => $product,
                        'zone_id' => $zone->id,
                        'month'   => $month,
                    ],
                    [
                        'avg_price' => 0,
                    ]
                );
            }
        }
    }
}


// function canViewLog()
// {
//     return in_array(request()->user()->user_role, ['sudo', 'etatique']);
// }


function logEvents()
{
    return [
        (object)  ['name' => 'Ajout', 'value' => 'ajout'],
        (object)  ['name' => 'Modification', 'value' => 'modification'],
        (object)  ['name' => 'Suppression', 'value' => 'suppression'],
        (object)  ['name' => 'Connexion', 'value' => 'connexion'],
        (object)  ['name' => 'Déconnexion', 'value' => 'déconnexion'],
    ];
}
