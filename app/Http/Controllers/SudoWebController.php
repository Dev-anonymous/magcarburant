<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\Fuel;
use App\Models\Fuelprice;
use App\Models\Label;
use App\Models\Structureprice;
use App\Models\Zone;
use Illuminate\Http\Request;

class SudoWebController extends Controller
{
    function home()
    {
        return view('sudo.home');
    }

    function provider()
    {
        // defaultdata();
        $item = request('item');
        if ($item) {
            $entity = Entity::where('shortname', $item)->first();
            if ($entity) {
                return view('sudo.apps', compact('entity'));
            }
        }
        $tx = request('tx');
        if ($tx) {
            $entity = Entity::where('shortname', $tx)->first();
            if ($entity) {
                return view('common.rates', compact('entity'));
            }
        }

        $price = request('price');
        if ($price) {
            $entity = Entity::where('shortname', $price)->first();
            if ($entity) {
                return view('common.prices', compact('entity'));
            }
        }

        $st = request('st');
        if ($st) {
            $structure = Structureprice::where('id', $st)->first();
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

        return view('sudo.provider');
    }

    function rates()
    {
        return view('sudo.structrates');
    }
}
