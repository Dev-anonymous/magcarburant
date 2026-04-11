<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fuelpricemining;
use Illuminate\Http\Request;

class FuelpriceminingController extends Controller
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
    public function show(Fuelpricemining $fuelpricemining)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fuelpricemining $fuelpricemining)
    {
        can('Structure des prix - Modifier', true);

        $validated = $request->validate([
            'price' => 'required|numeric',
        ]);
        $user = request()->user();
        abort_unless(isProLogEtaUser(), 403, "No permission");
        if (isPetroUser() || isLogUser()) {
            $parent = $user->user;
            if ($parent) $user = $parent;
            abort_if($fuelpricemining->structurepricemining->entity->users_id != $user->id, 403, "No permit");
        } else {
            //
        }

        $str = $fuelpricemining->structurepricemining()->first();
        // abort_if($fuelpricemining->zone->zone !==  "OUEST" && $fuelpricemining->label->tag === 'L', 403, "Can't edit");
        abort_if(in_array($fuelpricemining->labelmining->label, noteditable($fuelpricemining->fuel->fuel_type, $fuelpricemining->zone->zone, $str)), 403, "Can't edit");

        $fuelpricemining->amount = $request->price;
        $fuelpricemining->currency = 'USD';
        $fuelpricemining->save();
        return response()->json([
            'success' => true,
            'message' => 'OK'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fuelpricemining $fuelpricemining)
    {
        //
    }
}
