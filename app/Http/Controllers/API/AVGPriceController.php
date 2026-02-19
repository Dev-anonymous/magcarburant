<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AverageFuelPrice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AVGPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $year = request('year', nnow()->year);

        $rows = AverageFuelPrice::with('zone')
            ->whereYear('month', $year)
            ->orderBy('month')
            ->orderBy('product')
            ->get();

        $months = [];

        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = [];
            $filtered = $rows->filter(
                fn($r) => Carbon::parse($r->month)->month === $m
            );
            foreach ($filtered as $row) {
                $months[$m][$row->product][$row->zone->zone] = [
                    'id' => $row->id,
                    'price' => v($row->avg_price),
                    'v' => $row->avg_price,
                    'prod' => $row->product,
                ];
            }
        }

        return response()->json([
            'year'   => $year,
            'months' => $months,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['etatique']), 403, "No permission");

        $validated = $request->validate([
            'nord_id'  => 'required|numeric|exists:average_fuel_prices,id',
            'nord'  => 'required|numeric|min:0',
            'sud_id'  => 'required|numeric|exists:average_fuel_prices,id',
            'sud'  => 'required|numeric|min:0',
            'est_id'  => 'required|numeric|exists:average_fuel_prices,id',
            'est'  => 'required|numeric|min:0',
            'ouest_id'  => 'required|numeric|exists:average_fuel_prices,id',
            'ouest'  => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        $north = AverageFuelPrice::findOrFail($validated['nord_id']);
        $north->avg_price = $validated['nord'];
        $north->save();

        $south = AverageFuelPrice::findOrFail($validated['sud_id']);
        $south->avg_price = $validated['sud'];
        $south->save();

        $est = AverageFuelPrice::findOrFail($validated['est_id']);
        $est->avg_price = $validated['est'];
        $est->save();

        $ouest = AverageFuelPrice::findOrFail($validated['ouest_id']);
        $ouest->avg_price = $validated['ouest'];
        $ouest->save();
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "Le prix a été mis à jour avec succès !",
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AverageFuelPrice $avgprice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AverageFuelPrice $avgprice) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AverageFuelPrice $avgprice)
    {
        //
    }
}
