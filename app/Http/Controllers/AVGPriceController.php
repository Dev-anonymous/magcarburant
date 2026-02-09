<?php

namespace App\Http\Controllers;

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
        $rows = AverageFuelPrice::whereYear('month', $year)
            ->orderBy('month')
            ->orderBy('product')
            ->get()
            ->map(function ($row) {
                return [
                    'id'    => $row->id,
                    'product'    => $row->product,
                    'v'  => v($row->avg_price),
                    'avg_price' => $row->avg_price,
                    'month'      => $row->month,
                ];
            });

        $months = [];

        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = $rows
                ->filter(fn($r) => Carbon::parse($r['month'])->month === $m)
                ->values();
        }

        return response()->json([
            'year'   => $year,
            'months' => $months,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

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
    public function update(Request $request, AverageFuelPrice $avgprice)
    {
        $user = auth()->user();
        abort_if(!in_array($user->user_role, ['etatique']), 403, "No permission");

        $validated = $request->validate([
            'avg_price'  => 'required|numeric|min:0',
        ]);

        $avgprice->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Le prix a été mis à jour avec succès !",
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AverageFuelPrice $avgprice)
    {
        //
    }
}
