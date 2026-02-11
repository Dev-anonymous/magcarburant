<?php

namespace App\Http\Controllers;

use App\Models\AverageFuelPrice;
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

    function apps($mode, Entity $entity)
    {
        return view('state.views.apps', compact('entity'));
    }

    function purchase($mode, Entity $entity)
    {
        return view('state.views.purchase', compact('entity'));
    }

    function sale($mode, Entity $entity)
    {
        return view('state.views.sale', compact('entity'));
    }

    function delivery($mode, Entity $entity)
    {
        return view('state.views.delivery', compact('entity'));
    }

    function accounting($mode, Entity $entity)
    {
        $item = request('item');
        if ($item == 'rtx') {
            return view('state.views.rrates', compact('entity'));
        }

        if ($item == 'stx') {
            return view('state.views.strates', compact('entity'));
        }

        if ($item == 'pricestr') {
            return view('state.views.structprices', compact('entity'));
        }

        if ($item == 'gb') {
            return view('state.views.greatebook', compact('entity'));
        }

        if ($item == 'cc') {
            return view('state.views.greatebookCR', compact('entity'));
        }

        if ($item == 'pf') {
            return view('state.views.greatebookparafisc', compact('entity'));
        }

        $stx = request('stx');
        if ($stx) {
            $structure = $entity?->structureprices()->with(['fuelprices.fuel', 'fuelprices.zone', 'fuelprices.label'])->find($stx);
            if ($structure) {
                initfuelprice($structure);
                $structure->refresh();

                $terrestre = ['ESSENCE', 'GASOIL', 'PETROLE', 'FOMI'];
                $aviation  = ['JET'];
                $grouped = [
                    'terrestre' => [],
                    'aviation'  => [],
                ];
                foreach ($structure->fuelprices as $price) {
                    $fuelName  = strtoupper($price->fuel->fuel);
                    $zoneName  = $price->zone->zone;
                    $labelName = $price->label->label;
                    $labelTag  = $price->label->tag;

                    $type = in_array($fuelName, $terrestre) ? 'terrestre' : 'aviation';

                    if (!isset($grouped[$type][$zoneName][$fuelName])) {
                        $grouped[$type][$zoneName][$fuelName] = [];
                    }

                    $grouped[$type][$zoneName][$fuelName][$labelName] = [
                        'id' => $price->id,
                        'amount' => $price->amount,
                        'tag'    => $labelTag,
                    ];
                }
                
                return view('common.strprices', compact('grouped', 'structure'));
            }
        }
        return view('state.views.apps-accounting', compact('entity'));
    }

    function analyse($mode, Entity $entity)
    {
        return view('state.views.analyse', compact('entity'));
    }

    function claim($mode, Entity $entity)
    {
        return view('state.views.claim', compact('entity'));
    }

    function taxation($mode, Entity $entity)
    {
        return view('state.views.taxation', compact('entity'));
    }

    function avg_price($mode, Entity $entity)
    {
        initAvgPrice();
        $years = AverageFuelPrice::selectRaw('YEAR(month) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('state.views.avg_price', compact('years'));
    }
}
