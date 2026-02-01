<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\Structureprice;
use App\Models\User;

class StateWebController extends Controller
{
    function home()
    {
        $entity = auth()->user()->entities()->first();
        $entities = Entity::whereIn('users_id', User::whereIn('user_role', ['logisticien', 'petrolier'])->pluck('id'))->orderBy('shortname', 'asc')->get();
        return view('state.choose', compact('entity', 'entities'));
    }

    function apps(Entity $entity)
    {
        return view('state.views.apps', compact('entity'));
    }

    function purchase(Entity $entity)
    {
        return view('state.views.purchase', compact('entity'));
    }

    function sale(Entity $entity)
    {
        return view('state.views.sale', compact('entity'));
    }

    function delivery(Entity $entity)
    {
        return view('state.views.delivery', compact('entity'));
    }


    function accounting(Entity $entity)
    {
        $item = request('item');
        // if ($item == 'rtx') {
        //     $user = auth()->user();
        //     $entity = $user->entities()->first();
        //     return view('common.rates', compact('entity'));
        // }

        // if ($item == 'stx') {
        //     $user = auth()->user();
        //     $entity = $user->entities()->first();
        //     return view('provider.structrates', compact('entity'));
        // }

        // if ($item == 'pricestr') {
        //     $user = auth()->user();
        //     $entity = $user->entities()->first();
        //     return view('common.structprices', compact('entity'));
        // }

        if ($item == 'gb') {
            return view('state.views.greatebook', compact('entity'));
        }

        // if ($item == 'cc') {
        //     return view('provider.greatebookCR');
        // }

        // if ($item == 'pf') {
        //     return view('provider.greatebookparafisc');
        // }

        // $stx = request('stx');
        // if ($stx) {
        //     $entity = auth()->user()->entities()->first();
        //     $structure = $entity?->structureprices()->with(['fuelprices.fuel', 'fuelprices.zone', 'fuelprices.label'])->find($stx);
        //     if ($structure) {
        //         initfuelprice($structure);
        //         $structure->refresh();

        //         $terrestre = ['ESSENCE', 'GASOIL', 'PETROLE', 'FOMI'];
        //         $aviation  = ['JET'];
        //         $grouped = [
        //             'terrestre' => [],
        //             'aviation'  => [],
        //         ];
        //         foreach ($structure->fuelprices as $price) {
        //             $fuelName  = strtoupper($price->fuel->fuel);
        //             $zoneName  = $price->zone->zone;
        //             $labelName = $price->label->label;
        //             $labelTag  = $price->label->tag;

        //             $type = in_array($fuelName, $terrestre) ? 'terrestre' : 'aviation';

        //             if (!isset($grouped[$type][$zoneName][$fuelName])) {
        //                 $grouped[$type][$zoneName][$fuelName] = [];
        //             }

        //             $grouped[$type][$zoneName][$fuelName][$labelName] = [
        //                 'id' => $price->id,
        //                 'amount' => $price->amount,
        //                 'tag'    => $labelTag,
        //             ];
        //         }

        //         return view('common.strprices', compact('grouped', 'structure'));
        //     }
        // }
        return view('state.views.apps-accounting', compact('entity'));
    }

    function analyse(Entity $entity)
    {
        $user = auth()->user();
        return view('state.views.analyse', compact('entity'));
    }

    function claim(Entity $entity)
    {
        return view('state.views.claim', compact('entity'));
    }


    ////////// ///////////////////////////////////////////////////////////////////////











    function taxation()
    {
        return view('provider.taxation');
    }
}
