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
            $structure = @$entity?->structureprices()->where('id', $st)->first();
            if ($structure) {
                $fp = $structure->fuelprices()->first();
                if (!$fp) {
                    DB::beginTransaction();
                    $zones  = Zone::all()->keyBy('zone');
                    $fuels  = Fuel::all()->keyBy('fuel');
                    $labels = Label::all()->keyBy('tag');

                    foreach ($zones as $z) {
                        $zone = Zone::where('zone', $z)->first();
                        if (!$zone) continue;
                        if ($zone) {
                            foreach ($fuels as $f) {
                                $fuel = Fuel::where('fuel', $f)->first();
                                if (!$fuel) continue;
                                foreach ($labels as $lt => $l) {
                                    $label = Label::where('tag', $lt)->first();
                                    if (!$label) continue;
                                    if ('OUEST' !== $z && $lt === 'H') {
                                        continue;
                                    }
                                    $fp = new Fuelprice;
                                    $fp->structureprice_id = $structure->id;
                                    $fp->fuel_id = $fuel->id;
                                    $fp->label_id = $label->id;
                                    $fp->zone_id = $zone->id;
                                    $fp->amount = 0;
                                    $fp->save();
                                }
                            }
                        }
                    }
                    DB::commit();
                }

                return view('provider.strprices', compact('structure', 'zones'));
            }
        }
        return view('provider.prices');
    }
}
