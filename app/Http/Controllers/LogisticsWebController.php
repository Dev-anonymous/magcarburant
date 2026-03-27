<?php

namespace App\Http\Controllers;

use App\Models\Structureprice;

class LogisticsWebController extends Controller
{
    function home()
    {
        $entity = gentity();
        return view('logistics.apps', compact('entity'));
    }

    function dash()
    {
        can('Tableau de bord - Lire', true);
        return view('common.dash');
    }

    function sale()
    {
        can('Vente - Lire', true);
        return view('logistics.sale');
    }

    // function mining_sale()
    // {
    //     return view('logistics.mining_sale');
    // }

    function accounting()
    {
        can('Comptabilité - Lire', true);

        $item = request('item');
        if ($item == 'rtx') {
            can('Taux réels - Lire', true);
            $entity = gentity();
            return view('common.rates', compact('entity'));
        }

        if ($item == 'stx') {
            can('Taux structures - Lire', true);
            $entity = gentity();
            return view('provider.structrates', compact('entity'));
        }

        if ($item == 'pricestr') {
            can('Structure des prix - Lire', true);
            $entity = gentity();
            return view('common.structprices', compact('entity'));
        }

        if ($item == 'gb') {
            can('Grand livre manque à gagner - Lire', true);
            return view('logistics.greatebook');
        }

        $stx = request('stx');
        if ($stx) {
            $entity = gentity();
            $structure = $entity?->structureprices()->with(['fuelprices.fuel', 'fuelprices.zone', 'fuelprices.label'])->find($stx);
            if ($structure) {
                can('Structure des prix - Modifier', true);
                
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
        return view('logistics.apps-accounting');
    }

    function analyse()
    {
        can('Bilan manque à gagner - Lire', true);
        $entity = gentity();
        $ps = Structureprice::where('entity_id', $entity->id)->orderByDesc('id')->get();
        return view('logistics.analyse', compact('ps'));
    }
}
