<?php

use App\Models\AverageFuelPrice;
use App\Models\Entity;
use App\Models\Fuel;
use App\Models\Fuelprice;
use App\Models\Fuelpricemining;
use App\Models\Label;
use App\Models\Labelmining;
use App\Models\SecurityStock;
use App\Models\StateFuelprice;
use App\Models\StateStructureprice;
use App\Models\Structureprice;
use App\Models\Structurepricemining;
use App\Models\User;
use App\Models\Zone;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

function nnow()
{
    return now('Africa/Lubumbashi');
}

function noteditable($type, $zone, Structureprice|Structurepricemining|StateStructureprice $structure)
{
    $type = strtolower($type);
    $zone = strtolower($zone);

    if ($structure instanceof Structureprice || $structure instanceof StateStructureprice) {
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
    } else {
        $noedit = [
            'terrestre' => [
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
            ],
        ];
    }
    return (array) @$noedit[$type][$zone];
}

function uneditable(Structureprice|Structurepricemining $structure)
{
    $ignore = [];
    foreach (['aviation', 'terrestre'] as $t) {
        foreach (mainWays() as $z) {
            $ignore = [...$ignore, ...noteditable($t, $z, $structure)];
        }
    }
    return array_values(array_unique($ignore));
}

function mainfuels($isminier = false)
{
    return $isminier ?  ['ESSENCE', 'PETROLE', 'GASOIL'] :  ['ESSENCE', 'PETROLE', 'GASOIL', 'FOMI', 'JET'];
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


function initfuelpricemining(Structurepricemining $structure)
{
    $isState = !($structure instanceof Structurepricemining);

    if ($isState) {
        dd('stta');
    } else {
        $exists = Fuelpricemining::where(['structurepricemining_id' => $structure->id])->exists();
    }

    if ($exists) return;

    $zoneLabels = [
        'Sud' => [
            'terrestre' => [
                "PMFC en M3",
                "Charges d'exploitation logisticiens (frais d'entreprot)",
                "Total frais des sociétés de logistique",
                "Charges d'exploitation Sociétés commerciales",
                "Marges Sociétés Commerciales (10% PMF)",
                "Total frais des sociétés Commerciales",
                "Stock de sécurité 1",
                "Stock de sécurité 2",
                "Marquage moléculaire",
                "FONER (Fonds National d'Entretien Routier)",
                "Effort de reconstruction et Stock Stratégiques",
                "Interventions Economiques",
                "Total Parafiscalité",
                "PMF fiscal (PMFF=Ki*PMFC)",
                "TVA à la vente (TVAV) pour calcul",
                "Droits de douane (10% PMF Commercial)",
                "Droits de consommation (25%, 15%, 0% du PMFF)",
                "TVA à l'importation (TVAI) = 16%(PMFC+DD+DC)",
                "Total Fiscalité 1",
                "TVA nette à l'intérieur (TVAIr=TVAV-TVAI)",
                "Total Fiscalité 2",
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)',
            ],
        ],
    ];

    foreach ($zoneLabels as $zoneName => $types) {
        $zone = Zone::where('zone', $zoneName)->first();
        foreach ($types as $fuelType => $labels) {
            $fuels = Fuel::whereIn('fuel', ['ESSENCE', 'GASOIL', 'PETROLE'])->get();
            foreach ($fuels as $fuel) {
                foreach ($labels as $labelName) {
                    $label = Labelmining::where('label', $labelName)->first();
                    if ($label && $zone) {
                        if ($isState) {
                            abort('e404');
                        } else {
                            $exists = Fuelpricemining::where('structurepricemining_id', $structure->id)
                                ->where('fuel_id', $fuel->id)
                                ->where('zone_id', $zone->id)
                                ->where('labelmining_id', $label->id)
                                ->exists();
                        }
                        if (! $exists) {
                            if ($isState) {
                                //
                            } else {
                                Fuelpricemining::create([
                                    'structurepricemining_id' => $structure->id,
                                    'fuel_id' => $fuel->id,
                                    'zone_id' => $zone->id,
                                    'labelmining_id' => $label->id,
                                    'amount' => 0,
                                    'currency' => 'USD',
                                ]);
                            }
                        }
                    }
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
        if ($role === 'utilisateur') {
            $parent = $user->user;
            $e = $parent?->entities()->first()?->logo;
            if ($e) {
                $i  = "storage/$e";
            }
            return asset($i);
        }
        return asset($i);
    }
}

function strname(?Entity $entity, Structureprice|Structurepricemining|StateStructureprice $str)
{
    if ($str instanceof Structureprice) {
        $n = $entity->structureprices()->whereNotNull('name')->count() + 1;
    } elseif ($str instanceof Structurepricemining) {
        $n = $entity->structurepriceminings()->whereNotNull('name')->count() + 1;
    } elseif ($str instanceof StateStructureprice) {
        $n = StateStructureprice::whereNotNull('name')->count() + 1;
    } else {
        abort(" Erreur strname() ");
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
    if (isLogUser()) {
        $name = gentity()->shortname;
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
    if (isPetroUser()) {
        $href = route($route ? "provider.$route" : 'provider.accounting', $params);
    } elseif (isLogUser()) {
        $href = route('logistics.accounting', $params);
    } elseif (isEtaUser()) {
        $mode = rmode();
        $entity  = Entity::findOrFail(request('entity_id'));
        $params = array_merge(['entity' => $entity->id, 'mode' => $mode], $params);
        $href = route($route ? "state.$route" : 'state.accounting', $params);
    } else {
        abort(422, "Invalid role ! gb_href");
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
    return isEtaUser() && rmode() === 'edit';
}


function canmutuality(Entity $entity, String $product)
{
    $productsByCompany = [
        'SOCIR'     => ['ESSENCE', 'PETROLE', 'GASOIL', 'JET'],
        'LEREXCOM'  => ['ESSENCE', 'GASOIL'],
        'SPSA'      => ['ESSENCE', 'GASOIL', 'JET'],
        'SEP CONGO' => ['ESSENCE', 'PETROLE', 'GASOIL', 'JET'],
    ];
    $companyName = $entity->shortname;

    return isset($productsByCompany[$companyName]) && in_array($product, $productsByCompany[$companyName]);
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

function initStockPrice()
{
    $year = now()->year;
    $user = Auth::user();
    $fromState = from_state();
    $entities = Entity::whereIn('users_id', User::whereIn('user_role', ['petrolier', 'logisticien'])->pluck('id'))->get();
    $months = CarbonPeriod::create("$year-01-01", '1 month', "$year-12-01");
    foreach ($entities as $entity) {
        foreach ($months as $month) {
            SecurityStock::firstOrCreate(
                [
                    'entity_id'  => $entity->id,
                    'month'      => $month->toDateString(),
                    'from_state' => $fromState,
                ],
                [
                    'amount' => 0,
                ]
            );
        }
    }
}

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

function middleTruncate(string $value): string
{
    $end = ' ... ';
    $limit = 20;
    $length = Str::length($value);

    if ($length <= $limit) {
        return $value;
    }

    $keep = (int) (($limit - Str::length($end)) / 2);

    $start = Str::substr($value, 0, $keep);
    $finish = Str::substr($value, -$keep);

    return $start . $end . $finish;
}

function ua()
{
    $agent = new Agent();
    $userAgentInfo = sprintf(
        "%s %s sur %s",
        $agent->browser(),
        $agent->version($agent->browser()),
        $agent->platform()
    );
    return $userAgentInfo;
}

function childrenlist(User $user, $withme = true)
{
    $t = [];
    if (isProLogEtaUser()) {
        $parent = $user->user;
        if ($parent) {
            $user = $parent; // role utilisateur -> liste audit
        }
        $t = $user->users()->pluck('id')->all();
        if ($withme) {
            $t = [$user->id, ...$user->users()->pluck('id')->all()];
        }
    }
    return $t;
}

function gentity(): Entity
{
    $user = request()->user();
    if (in_array($user->user_role, ['logisticien', 'etatique', 'petrolier'], true)) {
        return $user->entities()->first();
    }
    if ($user->user_role === 'utilisateur') {
        return $user->user?->entities()->first();
    }

    throw new Exception("User role not supported for getting entity");
}

function isProLogEtaUser()
{
    return isPetroUser() || isLogUser() || isEtaUser();
}


function isLogUser()
{
    $user = request()->user();
    $role = $user->user_role;
    $parent = $user->user;

    return
        $role === 'logisticien' || ($parent && $parent->user_role === 'logisticien' && $role === 'utilisateur');
}


function isPetroUser()
{
    $user = request()->user();
    $role = $user->user_role;
    $parent = $user->user;
    return
        $role === 'petrolier' || ($parent && $parent->user_role === 'petrolier' && $role === 'utilisateur');
}


function isEtaUser()
{
    $user = request()->user();
    $role = $user->user_role;
    $parent = $user->user;
    return
        $role === 'etatique' || ($parent && $parent->user_role === 'etatique' && $role === 'utilisateur');
}

function terminal()
{
    return Entity::whereIn('users_id', User::where('user_role', 'logisticien')->pluck('id'))->pluck('shortname')->map(function ($item) {
        return strtoupper($item);
    })->all();
}

function can(string|array $permissionNames, bool $abort = false): bool
{
    $user = request()->user();
    if (! $user) {
        return $abort ? abort(403, "Accès refusé [0]") : false;
    }

    $can = in_array($user->user_role, ['petrolier', 'logisticien', 'etatique'], true);

    if ($user->user_role === 'utilisateur') {
        if (is_string($permissionNames)) {
            $can = $user->role->permissions()
                ->where('name', $permissionNames)
                ->exists();
        } else {
            $can = $user->role->permissions()
                ->whereIn('name', $permissionNames)
                ->exists();
        }
    }

    if ($user->user_role === 'sudo') {
        $can = true;
    }

    if (! $can && $abort) {
        abort(403, "Accès refusé [0]");
    }

    return $can;
}

function statecan()
{
    $mode = rmode();
    $perm = 'Mode écriture - Lire';
    if ($mode == 'view') {
        $perm = 'Mode lecture - Lire';
    }
    can($perm, true);
}
