<?php

namespace App\Http\Controllers;

use App\Models\Fuel;
use App\Models\Fuelprice;
use App\Models\Label;
use App\Models\Structureprice;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderWebController extends Controller
{
    function home()
    {
        return view('provider.home');
    }

    function apps()
    {
        $entity = auth()->user()->entities()->first();
        return view('provider.apps', compact('entity'));
    }

    function rates()
    {
        $user = auth()->user();
        $entity = $user->entities()->first();
        return view('common.rates', compact('entity'));
    }
    function prices()
    {
        defaultdata();
        $st = request('st');
        if ($st) {
            $entity = auth()->user()->entities()->first();
            $structure = $entity?->structureprices()->find($st);
            initfuelprice($structure);

            if ($structure) {
                $zones = Zone::all();
                $fuels = Fuel::all();
                $labels = Label::all();

                $fuelprices = Fuelprice::where('structureprice_id', $structure->id)
                    ->get()
                    ->groupBy(['zone_id', 'fuel_id', 'label_id']);

                return view('common.strprices', compact('structure', 'zones', 'fuels', 'labels', 'fuelprices'));
            }
        }
        return view('common.prices');
    }
}
