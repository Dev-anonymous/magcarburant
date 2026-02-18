<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fuelprice;
use Illuminate\Http\Request;

class FuelpriceController extends Controller
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
    public function show(Fuelprice $fuelprice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fuelprice $fuelprice)
    {
        $validated = $request->validate([
            'price' => 'required|numeric',
        ]);
        $user = request()->user();
        abort_unless(in_array($user->user_role, ['petrolier', 'logisticien', 'etatique']), 403, "No permission");
        if (in_array($user->user_role, ['petrolier', 'logisticien'])) {
            abort_if($fuelprice->structureprice->entity->users_id != $user->id, 403, "No permit");
        } else {
            //
        }
        abort_if($fuelprice->zone->zone !==  "OUEST" && $fuelprice->label->tag === 'L', 403, "Can't edit");
        abort_if(in_array($fuelprice->label->label, noteditable($fuelprice->fuel->fuel_type, $fuelprice->zone->zone)), 403, "Can't edit");

        $fuelprice->amount = $request->price;
        $fuelprice->currency = 'USD';
        $fuelprice->save();
        return response()->json([
            'success' => true,
            'message' => 'OK'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fuelprice $fuelprice)
    {
        //
    }
}
