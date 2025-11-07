<?php

use Illuminate\Support\Facades\Auth;

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

    foreach($entities as $el){
        
    }
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
