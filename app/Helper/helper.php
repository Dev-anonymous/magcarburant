<?php

use App\Models\Entity;
use App\Models\Fuel;
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

    $zones = ['NORD', 'SUD', 'EST', 'OUEST'];
    foreach ($zones as $e) {
        $z = Zone::firstOrNew(['zone' => $e]);
        $z->save();
    }

    $fuels = ['ESSENCE', 'PETROLE', 'GASOILE', 'FOMI'];
    foreach ($fuels as $e) {
        $z = Fuel::firstOrNew(['fuel' => $e]);
        $z->save();
    }

    $labels = [
        'A' => 'PMF Commerciale (PMFC)',
        'B' => 'Frais & Services SOCIR',
        'C' => 'Charges d\'exploitation SEP, Strock de sécurité 7 stratégique',
        'D' => 'Frais de mise en place SEP',
        'E' => 'Stock de sécurité',
        'F' => 'Stock stratégique',
        'G' => 'Charges additionnelles SPSA',
        'H' => 'Lerexcom',
        'I' => 'Charges d\'exploitation Soc. Com.',
        'J' => 'Marges Sociétés commerciales',
        'K' => 'Total frais de distribution',
        'L' => 'Stock de sécurité EST & SUD',
        'M' => 'FONER (Fonds National d\'Entretien Routier)',
        'N' => 'PMF Fiscal (PMFF=Ki*PMFC)',
        'O' => 'TVA à la vente (TVAV) pour calcul',
        'P' => 'Droits de douane',
        'Q' => 'Droits de consommation (25%, 15%, 0% du PMFF)',
        'R' => 'TVA à l\'importation (TVAI) = 16%(PMFC+DD+DC)',
        'S' => 'Fiscalité 1',
        'T' => 'TVA nette à l\'intérieur (TVA Ir=TVAV-TVAI)',
        'U' => 'Fiscalité 2',
        'V' => 'Prix de référence réel (M3)',
        'W' => 'Prix de référence à appliquer',
    ];

    foreach ($labels as $t => $l) {
        $z = Label::firstOrNew(['tag' => $t]);
        $z->label = $l;
        $z->save();
    }

    DB::commit();
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
