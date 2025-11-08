<?php

use App\Models\Entity;
use App\Models\User;
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

    DB::commit();
}

function userimg()
{
    if (Auth::check()) {
        $role = auth()->user()->user_role;
        if ($role == 'sudo') {
            return asset('assets/images/avatar.png');
        }
    }
}
