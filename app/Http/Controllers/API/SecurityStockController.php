<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\SecurityStock;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SecurityStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        if (in_array($user->user_role, ['petrolier', 'logisticien', 'etatique'])) {
            if (in_array($user->user_role, ['petrolier', 'logisticien'])) {
                $entity = $user->entities()->first();
            } elseif (in_array($user->user_role, ['etatique'])) {
                $entity  = Entity::findOrFail(request('entity_id'));
            } else {
                abort(403);
            }
            abort_if(!$entity, 422, "No entity");
            $data = $entity->security_stocks()->where('from_state', from_state());
        } else {
            abort(403);
        }

        $year = request('year', nnow()->year);
        $data->whereYear('month', $year);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('month', function ($row) {
                $date = $row->month;
                $m = ucfirst($date->translatedFormat('F')) . ' ' . $date->format('Y');
                return $m;
            })
            ->addColumn('amount', function ($row) {
                return v($row->amount);
            })
            ->addColumn('action', function ($row) use ($user) {
                $date = $row->month;
                $m = ucfirst($date->translatedFormat('F')) . ' ' . $date->format('Y');
                $data = e(json_encode(array_merge($row->toArray(), ['monthname' => $m])));

                $t = "<button class='btn btn-sm btn-primary editdata'  data-data='$data'>
                        <i class='material-icons md-14 align-middle'>edit</i>
                        <span class='align-middle'>Modifier</span>
                    </button>";
                if ($user->user_role == 'etatique') {
                    if (from_state()) {
                        return $t;
                    }
                } else {
                    return $t;
                }
            })
            ->rawColumns(['action'])
            ->make(true);
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
    public function show(SecurityStock $securitystock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SecurityStock $securitystock)
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'etatique', 'etatique']), 403, "No permission");
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $securitystock->amount = request('amount');
        $securitystock->save();

        return response()->json([
            'success' => true,
            'message' => "Le montant a été mis à jour avec succès !",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SecurityStock $securitystock)
    {
        //
    }
}
