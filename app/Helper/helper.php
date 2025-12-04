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
    $entities = [
        ['TOTAL', 'TOTAL ENERGIES SA'],
        ['ENGEN', 'ENGEN RDC SA'],
        ['COBIL', 'COBIL SA'],
        ['SONAHYDROC', 'Société Nationale des Hydrocarbures du Congo'],
        ['LEREXCOM', 'LEREXCOM'],
        ['SEP CONGO', 'SEP CONGO'],
        ['SPSA', 'SPSA COBIL'],
        ['SOCIR', 'SOCIR'],
        ['GPDPP', 'GPDPP'],
        ['FEC', 'Fédération des Entreprises du Congo'],
        ['MINECO', 'Ministère de l\'Économie'],
        ['PRIMATURE', 'Primature (Cabinet du Premier Ministre)'],
        ['PRESIDENCE', 'Présidence de la République'],
        ['MINHYD', 'Ministère des Hydrocarbures'],
        ['DGDA', 'Direction Générale des Douanes et Accises'],
        ['DGI', 'Direction Générale des Impôts'],
        ['AUTHENTIX', 'AUTHENTIX']
    ];

    DB::beginTransaction();
    foreach ($entities as $el) {
        $e = Entity::firstOrNew(['shortname' => $el[0]]);
        if (!$e->exists) {
            $email = strtolower($el[0]) . "@email.com";
            $u = User::where(['email' => $email])->firstOrNew();
            if (!$u->exists) {
                $u->name = $el[0];
                $u->email = $email;
                $u->password = Hash::make('mdp@123');
                $u->user_role = 'provider';
                $u->save();
            }

            $e->longname =  $el[1];
            $e->users_id = $u->id;
            $e->save();
        }
    }

    $zones = mainWays();
    foreach ($zones as $e) {
        $z = Zone::firstOrNew(['zone' => $e]);
        $z->save();
    }

    $fuels = mainfuels();
    foreach ($fuels as $e) {
        $z = Fuel::firstOrNew(['fuel' => $e]);
        $z->save();
    }

    $labels = [
        'A' => 'PMF Commerciale (PMFC)',
        'B' => 'Frais & Services SOCIR',
        'C' => 'Charges d\'exploitation SEP, Strock de sécurité & stratégique',
        'D' => 'Frais de mise en place SEP',
        'E' => 'Stock de sécurité',
        'F' => 'Stock stratégique',
        'G' => 'Lutte contre la fraude',
        'H' => 'Lutte contre la pollutiom',
        'I' => 'Autres entrepôts EST/SUD',
        'J' => 'Autres entrepôts SUD',
        'K' => 'Charges additionnelles SPSA',
        'L' => 'Lerexcom',
        'M' => 'Charges d\'exploitation Soc. Com.',
        'N' => 'Marges Sociétés commerciales',
        'O' => 'Total frais de distribution',
        'P' => 'Stock de sécurité EST & SUD',
        'Q' => 'FONER (Fonds National d\'Entretien Routier)',
        'R' => 'PMF Fiscal (PMFF=Ki*PMFC)',
        'S' => 'TVA à la vente (TVAV) pour calcul',
        'T' => 'Droits de douane',
        'U' => 'Droits de consommation (25%, 15%, 0% du PMFF)',
        'V' => 'TVA à l\'importation (TVAI) = 16%(PMFC+DD+DC)',
        'W' => 'Total Fiscalité 1',
        'X' => 'TVA nette à l\'intérieur (TVA Ir=TVAV-TVAI)',
        'Y' => 'Total Fiscalité 2',
        'Z' => 'Prix de référence réel (M3)',
        'AA' => 'Prix de référence à appliquer (L)',
    ];

    foreach ($labels as $t => $l) {
        $z = Label::firstOrNew(['tag' => $t]);
        $z->label = $l;
        $z->save();
    }

    DB::commit();
}

function initfuelprice(Structureprice $structure)
{
    $zones  = Zone::all()->keyBy('zone');     // ex: ['OUEST' => ZoneObj, 'EST' => ...]
    $fuels  = Fuel::all()->keyBy('fuel');     // ex: ['essence' => FuelObj, ...]
    $labels = Label::all()->keyBy('tag');     // ex: ['A' => LabelObj, 'B' => LabelObj, ...]

    $rowsToInsert = [];
    foreach ($zones as $zoneName => $zone) {
        foreach ($fuels as $fuelName => $fuel) {
            foreach ($labels as $labelTag => $label) {
                if ($zoneName !== 'OUEST' && $labelTag === 'L') {
                    continue;
                }
                Fuelprice::firstOrCreate(
                    [
                        'structureprice_id' => $structure->id,
                        'zone_id'           => $zone->id,
                        'fuel_id'           => $fuel->id,
                        'label_id'          => $label->id,
                    ],
                    [
                        'amount'   => null,
                        'currency' => null,
                    ]
                );
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
