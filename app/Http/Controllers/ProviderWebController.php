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
        return view('provider.rates');
    }
    function prices()
    {
        defaultdata();
        $st = request('st');
        if ($st) {
            $entity = auth()->user()->entities()->first();
            $structure = $entity?->structureprices()->find($st);

            if ($structure && !$structure->fuelprices()->exists()) {
                $zones  = Zone::all()->keyBy('zone');     // ex: ['OUEST' => ZoneObj, 'EST' => ...]
                $fuels  = Fuel::all()->keyBy('fuel');     // ex: ['essence' => FuelObj, ...]
                $labels = Label::all()->keyBy('tag');     // ex: ['A' => LabelObj, 'B' => LabelObj, ...]

                $rowsToInsert = [];
                foreach ($zones as $zoneName => $zone) {
                    foreach ($fuels as $fuelName => $fuel) {
                        foreach ($labels as $labelTag => $label) {
                            if ($zoneName !== 'OUEST' && $labelTag === 'H') {
                                continue;
                            }

                            $rowsToInsert[] = [
                                'structureprice_id' => $structure->id,
                                'zone_id'           => $zone->id,
                                'fuel_id'           => $fuel->id,
                                'label_id'          => $label->id,
                                'amount'            => null,
                                'currency'        => null,
                            ];
                        }
                    }
                }
                Fuelprice::insert($rowsToInsert);
            }
            if($structure){
                $zones = Zone::with(['fuelprices.fuel', 'fuelprices.label'])->get();
                return view('provider.strprices', compact('structure', 'zones'));
            }
        }
        return view('provider.prices');
    }
}
