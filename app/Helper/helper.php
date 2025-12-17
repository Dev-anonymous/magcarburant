<?php

use App\Models\Entity;
use App\Models\Fuel;
use App\Models\Fuelprice;
use App\Models\Label;
use App\Models\Structureprice;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function nnow()
{
    return now('Africa/Lubumbashi');
}

function noteditable()
{
    return ['D', 'O', 'W', 'X', 'Y', 'Z', 'AA'];
}

function mainfuels()
{
    return ['ESSENCE', 'PETROLE', 'GASOIL', 'FOMI', 'JET'];
}

function mainWays()
{
    return ['NORD', 'SUD', 'EST', 'OUEST'];
}

function defaultdata()
{


    // $labels = [
    //     'A' => 'PMF Commerciale (PMFC)',
    //     'B' => 'Frais & Services SOCIR',
    //     'C' => 'Charges d\'exploitation SEP, Strock de sécurité & stratégique',
    //     'D' => 'Frais de mise en place SEP',
    //     'E' => 'Stock de sécurité',
    //     'F' => 'Stock stratégique',
    //     'G' => 'Lutte contre la fraude',
    //     'H' => 'Lutte contre la pollutiom',
    //     'I' => 'Autres entrepôts EST/SUD',
    //     'J' => 'Autres entrepôts SUD',
    //     'K' => 'Charges additionnelles SPSA',
    //     'L' => 'Lerexcom',
    //     'M' => 'Charges d\'exploitation Soc. Com.',
    //     'N' => 'Marges Sociétés commerciales',
    //     'O' => 'Total frais de distribution',
    //     'P' => 'Stock de sécurité EST & SUD',
    //     'Q' => 'FONER (Fonds National d\'Entretien Routier)',
    //     'R' => 'PMF Fiscal (PMFF=Ki*PMFC)',
    //     'S' => 'TVA à la vente (TVAV) pour calcul',
    //     'T' => 'Droits de douane',
    //     'U' => 'Droits de consommation (25%, 15%, 0% du PMFF)',
    //     'V' => 'TVA à l\'importation (TVAI) = 16%(PMFC+DD+DC)',
    //     'W' => 'Total Fiscalité 1',
    //     'X' => 'TVA nette à l\'intérieur (TVA Ir=TVAV-TVAI)',
    //     'Y' => 'Total Fiscalité 2',
    //     'Z' => 'Prix de référence réel (M3)',
    //     'AA' => 'Prix de référence à appliquer (L)',
    // ];

    // foreach ($labels =[] as $t => $l) {
    //     $z = Label::firstOrNew(['tag' => $t]);
    //     $z->label = $l;
    //     $z->save();
    // }


    // Tous les labels terrestres et aviation, sans les numéros
    // $excelLabels = [
    //     'Platts',
    //     'Premium/TM',
    //     'PMFC en TM',
    //     'Densité',
    //     'PMFC en M3',
    //     'Charges SOCIR',
    //     'Charges Sep Congo',
    //     'Charges SPSA-COBIL',
    //     'Charges LEREXCOM PETROLEUM ET Appui Terrestre',
    //     'Total frais des sociétés de logistique',
    //     'Charges d\'exploitation Sociétés commerciales',
    //     'Marges Sociétés Commerciales (10% PMF)',
    //     'Total frais des sociétés Commerciales',
    //     'Stock de sécurité 1',
    //     'Stock de sécurité 2',
    //     'Effort de reconstruction et Stock Stratégiques',
    //     'CRP & Comité de suivi des Prix des produits Petroliers',
    //     'Marquage moléculaire',
    //     'FONER (Fonds National d\'Entretien Routier)',
    //     'Interventions Economiques',
    //     'Total Parafiscalité',
    //     'PMF fiscal (PMFF=Ki*PMFC)',
    //     'TVA à la vente (TVAV) pour calcul',
    //     'Droits de douane (10% PMF Commercial)',
    //     'Droits de consommation (25%, 15%, 0% du PMFF)',
    //     'TVA à l\'importation (TVAI) = 16%(PMFC+DD+DC)',
    //     'Total Fiscalité 1',
    //     'TVA nette à l\'intérieur (TVAIr=TVAV-TVAI)',
    //     'Total Fiscalité 2',
    //     'Prix de référence réel (USD/M3)',
    //     'Prix de référence à appliquer (USD/L)',

    //     'Frais & Services SOCIR',
    //     'Charges d\'exploitation Sep Congo',
    //     'Charges capacités additionnelles SPSA',
    //     'Charges d\'exploitation logisticien (Frais d\'entrepot)',
    // ];

    // function numberToExcelColumn($num)
    // {
    //     $column = '';
    //     while ($num >= 0) {
    //         $column = chr($num % 26 + 65) . $column;
    //         $num = intval($num / 26) - 1;
    //     }
    //     return $column;
    // }

    // $tagAscii = 65;
    // foreach ($excelLabels as $index =>  $labelText) {
    //     $tag = numberToExcelColumn($index);

    //     Label::firstOrCreate(
    //         ['label' => $labelText],
    //         ['tag' => $tag]
    //     );
    //     $tagAscii++;
    // }



    DB::commit();
}

function initfuelprice(Structureprice $structure)
{
    $zoneLabels = [
        'Nord' => [
            'terrestre' => [
                'Platts',
                'Premium/TM',
                'PMFC en TM',
                'Densité',
                'PMFC en M3',
                'Charges Sep Congo et Autres entrepots',
                'Total frais des sociétés de logistique',
                'Charges d\'exploitation Sociétés commerciales',
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'Stock de sécurité 1',
                'Stock de sécurité 2',
                'Marquage moléculaire',
                'FONER (Fonds National d\'Entretien Routier)',
                'Total Parafiscalité',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV)',
                'Droits de douane',
                'Droits de consommation',
                'TVA à l\'importation',
                'Total Fiscalité 1',
                'TVA nette à l\'intérieur',
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)'
            ]
        ],
        'Sud' => [
            'terrestre' => [
                'PMFC en M3',
                'Charges d\'exploitation logisticiens',
                'Total frais des sociétés de logistique',
                'Charges d\'exploitation Sociétés commerciales',
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'Stock de sécurité 1',
                'Stock de sécurité 2',
                'Marquage moléculaire',
                'FONER (Fonds National d\'Entretien Routier)',
                'Effort de reconstruction et Stock Stratégiques',
                'Interventions Economiques',
                'Total Parafiscalité',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV)',
                'Droits de douane',
                'Droits de consommation',
                'TVA à l\'importation',
                'Total Fiscalité 1',
                'TVA nette à l\'intérieur',
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)'
            ],
            'aviation' => [
                'PMFC en M3',
                'Charges d\'exploitation logisticien',
                'Total frais des sociétés de logistique',
                'Charges d\'exploitation Sociétés commerciales',
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV)',
                'Droits de douane',
                'Droits de consommation',
                'TVA à l\'importation',
                'Total Fiscalité 1',
                'TVA nette à l\'intérieur',
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)'
            ]
        ],
        'Est' => [
            'terrestre' => [
                'PMFC en M3',
                'Charges d\'exploitation logisticiens',
                'Capacités additionnelles KPS',
                'Total frais des sociétés de logistique',
                'Charges d\'exploitation Sociétés commerciales',
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'Stock de sécurité 1',
                'Stock de sécurité 2',
                'Marquage moléculaire',
                'FONER (Fonds National d\'Entretien Routier)',
                'Effort de reconstruction et Stock Stratégiques',
                'Interventions Economiques',
                'Total Parafiscalité',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV)',
                'Droits de douane',
                'Droits de consommation',
                'TVA à l\'importation',
                'Total Fiscalité 1',
                'TVA nette à l\'intérieur',
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)'
            ],
            'aviation' => [
                'PMFC en M3',
                'Charges d\'exploitation logisticien',
                'Total frais des sociétés de logistique',
                'Charges d\'exploitation Sociétés commerciales',
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV)',
                'Droits de douane',
                'Droits de consommation',
                'TVA à l\'importation',
                'Total Fiscalité 1',
                'TVA nette à l\'intérieur',
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)'
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
                'Charges d\'exploitation Sociétés commerciales',
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'Stock de sécurité 1',
                'Stock de sécurité 2',
                'Effort de reconstruction et Stock Stratégiques',
                'CRP & Comité de suivi des Prix des produits Petroliers',
                'Marquage moléculaire',
                'FONER (Fonds National d\'Entretien Routier)',
                'Interventions Economiques',
                'Total Parafiscalité',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV)',
                'Droits de douane',
                'Droits de consommation',
                'TVA à l\'importation',
                'Total Fiscalité 1',
                'TVA nette à l\'intérieur',
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)'
            ],
            'aviation' => [
                'PMFC en M3',
                'Frais & Services SOCIR',
                'Charges d\'exploitation Sep Congo',
                'Charges capacités additionnelles SPSA',
                'Total frais des sociétés de logistique',
                'Charges d\'exploitation Sociétés commerciales',
                'Marges Sociétés Commerciales (10% PMF)',
                'Total frais des sociétés Commerciales',
                'PMF fiscal (PMFF=Ki*PMFC)',
                'TVA à la vente (TVAV)',
                'Droits de douane',
                'Droits de consommation',
                'TVA à l\'importation',
                'Total Fiscalité 1',
                'TVA nette à l\'intérieur',
                'Total Fiscalité 2',
                'Prix de référence réel (USD/M3)',
                'Prix de référence à appliquer (USD/L)'
            ]
        ],
    ];

    // 2️⃣ Boucle sur zones et labels valides
    foreach ($zoneLabels as $zoneName => $types) {
        $zone = Zone::where('zone', $zoneName)->first();
        foreach ($types as $fuelType => $labels) {
            $fuels = Fuel::where('fuel_type', $fuelType)->get();
            foreach ($fuels as $fuel) {
                foreach ($labels as $labelName) {
                    $label = Label::where('label', $labelName)->first();
                    if ($label && $zone) {
                        $exists = FuelPrice::where('structureprice_id', $structure->id)
                            ->where('fuel_id', $fuel->id)
                            ->where('zone_id', $zone->id)
                            ->where('label_id', $label->id)
                            ->exists();

                        if (! $exists) {
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
                    // if (!$label) {
                    //     dd($labelName, $label, $zone->zone, $fuel->fuel);
                    // }
                }
            }
        }
    }
}

function userimg()
{
    if (Auth::check()) {
        $user = auth()->user();
        $role = $user->user_role;
        $i = 'assets/images/avatar.png';
        if ($role === 'sudo') {
            return asset($i);
        }
        if ($role === 'provider') {
            $e = $user->entities()->first()?->logo;
            if ($e) {
                $i  = "storage/$e";
            }
            return asset($i);
        }
    }
}

function strname(Entity $entity, Structureprice $str)
{
    $n = $entity->structureprices()->whereNotNull('name')->count() + 1;
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

function v($v, $decimal = 4)
{
    return number_format($v, $decimal, ',', ' ');
}
