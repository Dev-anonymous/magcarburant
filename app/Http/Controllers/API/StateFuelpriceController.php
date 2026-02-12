<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StateFuelprice;
use Illuminate\Http\Request;

class StateFuelpriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StateFuelprice $statefuelprice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StateFuelprice $statefuelprice)
    {
        $validated = $request->validate([
            'price' => 'required|numeric',
        ]);
        $user = auth()->user();
        abort_unless(in_array($user->user_role, ['etatique']), 403, "No permission");

        abort_if($statefuelprice->zone->zone !==  "OUEST" && $statefuelprice->label->tag === 'L', 403, "Can't edit");
        abort_if(in_array($statefuelprice->label->label, noteditable($statefuelprice->fuel->fuel_type, $statefuelprice->zone->zone)), 403, "Can't edit");

        $statefuelprice->amount = $request->price;
        $statefuelprice->currency = 'USD';
        $statefuelprice->save();
        return response()->json([
            'success' => true,
            'message' => 'OK'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StateFuelprice $statefuelprice)
    {
        //
    }
}
