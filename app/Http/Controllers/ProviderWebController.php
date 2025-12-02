<?php

namespace App\Http\Controllers;

use App\Models\Fuel;
use App\Models\Fuelprice;
use App\Models\Label;
use App\Models\Rate;
use App\Models\Structureprice;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderWebController extends Controller
{
    function home()
    {
        $entity = auth()->user()->entities()->first();
        return view('provider.apps', compact('entity'));
    }

    function accounting()
    {
        $item = request('item');
        if ($item == 'rtx') {
            $user = auth()->user();
            $entity = $user->entities()->first();
            return view('common.rates', compact('entity'));
        }

        $item = request('item');
        if ($item == 'stx') {
            $user = auth()->user();
            $entity = $user->entities()->first();
            return view('provider.structrates', compact('entity'));
        }

        $item = request('item');
        if ($item == 'pricestr') {
            $user = auth()->user();
            $entity = $user->entities()->first();
            return view('common.structprices', compact('entity'));
        }

        $stx = request('stx');
        if ($stx) {
            $entity = auth()->user()->entities()->first();
            $structure = $entity?->structureprices()->find($stx);
            if ($structure) {
                initfuelprice($structure);
                $zones = Zone::all();
                $fuels = Fuel::all();
                $labels = Label::all();

                $fuelprices = Fuelprice::where('structureprice_id', $structure->id)
                    ->get()
                    ->groupBy(['zone_id', 'fuel_id', 'label_id']);

                $from = $structure->from?->format('Y-m-d');
                $to = $structure->to ?? nnow()->toDateString();

                $tx = Rate::where('type', 'STRUCTURE')->where(function ($q) use ($from, $to) {
                    $q->where(function ($q) use ($from, $to) {
                        $q->where('from', '<=', $to)->where('to', '>=', $from);
                    })->orWhere(function ($q) use ($from, $to) {
                        $q->whereNull('to')->whereBetween('from', [$from, $to]);
                    });
                })->first();

                return view('common.strprices', compact('structure', 'tx', 'zones', 'fuels', 'labels', 'fuelprices'));
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

    function analyse()
    {
        return view('provider.analyse');
    }
}
