<?php

namespace App\Http\Controllers;

use App\Models\Fuel;
use App\Models\Fuelprice;
use App\Models\Label;
use App\Models\Rate;
use App\Models\Structureprice;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProviderWebController extends Controller
{
    function home()
    {
        $entity = request()->user()->entities()->first();
        return view('provider.apps', compact('entity'));
    }

    function dash(){
        return view('common.dash');
    }

    function accounting()
    {
        $item = request('item');
        if ($item == 'rtx') {
            $user = request()->user();
            $entity = $user->entities()->first();
            return view('common.rates', compact('entity'));
        }

        if ($item == 'stx') {
            $user = request()->user();
            $entity = $user->entities()->first();
            return view('provider.structrates', compact('entity'));
        }

        if ($item == 'pricestr') {
            $user = request()->user();
            $entity = $user->entities()->first();
            return view('common.structprices', compact('entity'));
        }

        if ($item == 'gb') {
            return view('provider.greatebook');
        }

        if ($item == 'cc') {
            return view('provider.greatebookCR');
        }

        if ($item == 'pf') {
            return view('provider.greatebookparafisc');
        }

        $stx = request('stx');
        if ($stx) {
            $entity = request()->user()->entities()->first();
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
        return view('provider.apps-accounting');
    }

    function purchase()
    {
        return view('provider.purchase');
    }

    function sale()
    {
        return view('provider.sale');
    }

    function mining_sale(){
        return view('provider.mining_sale');
    }

    function analyse()
    {
        $user = request()->user();
        $entity = $user->entities()->first();
        $ps = Structureprice::where('entity_id', $entity->id)->orderByDesc('id')->get();
        return view('provider.analyse', compact('ps'));
    }

    function claim()
    {
        $user = request()->user();
        $entity = $user->entities()->first();
        $ps = Structureprice::where('entity_id', $entity->id)->orderByDesc('id')->get();
        return view('provider.claim', compact('ps'));
    }

    function delivery()
    {
        return view('provider.delivery');
    }

    function taxation()
    {
        return view('provider.taxation');
    }
}
