<?php

namespace App\Http\Controllers;

use App\Models\Fuel;
use App\Models\Fuelprice;
use App\Models\Label;
use App\Models\Rate;
use App\Models\SecurityStock;
use App\Models\Structureprice;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Can;

class ProviderWebController extends Controller
{
    function home()
    {
        $entity = gentity();
        return view('provider.apps', compact('entity'));
    }

    function dash()
    {
        can('Tableau de bord - Lire', true);
        return view('common.dash');
    }

    function accounting()
    {
        can('Comptabilité - Lire', true);

        $item = request('item');
        if ($item == 'rtx') {
            $user = request()->user();
            $entity = gentity();
            return view('common.rates', compact('entity'));
        }

        if ($item == 'stx') {
            $user = request()->user();
            $entity = gentity();
            return view('provider.structrates', compact('entity'));
        }

        if ($item == 'pricestr') {
            can('Structure des prix - Lire', true);
            $user = request()->user();
            $entity = gentity();
            return view('common.structprices', compact('entity'));
        }

        if ($item == 'gb') {
            can('Grand livre manque à gagner - Lire', true);
            return view('provider.greatebook');
        }

        if ($item == 'cc') {
            can('Grand livre croisement des créances - Lire', true);
            return view('provider.greatebookCR');
        }

        if ($item == 'pf') {
            can('Grand livre fiscalité - Lire', true);
            return view('provider.greatebookparafisc');
        }

        $stx = request('stx');
        if ($stx) {
            $entity = gentity();
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
        can('Achat - Lire', true);
        return view('provider.purchase');
    }

    function sale()
    {
        can('Vente - Lire', true);
        return view('provider.sale');
    }

    function mining_sale()
    {
        can('Vente liées aux STEs minières - Lire', true);
        return view('provider.mining_sale');
    }

    function analyse()
    {
        can('Bilan manque à gagner - Lire', true);
        $entity = gentity();
        $ps = Structureprice::where('entity_id', $entity->id)->orderByDesc('id')->get();
        return view('provider.analyse', compact('ps'));
    }

    function claim()
    {
        can('Bilan croisement des créances - Lire', true);
        $entity = gentity();
        $ps = Structureprice::where('entity_id', $entity->id)->orderByDesc('id')->get();
        return view('provider.claim', compact('ps'));
    }

    function delivery()
    {
        can('Livraison excédentaire - Lire', true);
        return view('provider.delivery');
    }

    function taxation()
    {
        can('Bilan fiscalité - Lire', true);
        return view('provider.taxation');
    }

    function security_stock()
    {
        can('Stock de sécurité collecté reversé - Lire', true);
        initStockPrice();
        $years = SecurityStock::selectRaw('YEAR(month) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $entity = gentity();

        return view('provider.security_stock', compact('years', 'entity'));
    }
}
