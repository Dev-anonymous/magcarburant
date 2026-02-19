<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AccountingClosure;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AccountingClosureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['etatique']), 403, "No permission");

        $entity = Entity::findOrFail(request('entity_id'));

        $data = AccountingClosure::orderByDesc('id')->where('entity_id', $entity->id);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('closed_until', function ($row) {
                return $row->closed_until?->format('d-m-Y');
            })->editColumn('closed_by', function ($row) {
                return $row->user?->name;
            })
            ->rawColumns([])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['etatique']), 403, "No permission");

        $validated = $request->validate([
            'closed_until' => 'required|date|before_or_equal:today',
            'entity_id' => 'required|numeric|exists:entity,id',
        ], [
            'closed_until.required' => 'Veuillez renseigner la date de clôture de la cession.',
        ]);

        // Vérifier la dernière clôture existante pour cette entité
        $lastClosure = AccountingClosure::where('entity_id', $validated['entity_id'])
            ->orderBy('closed_until', 'desc')
            ->first();

        if ($lastClosure && $validated['closed_until'] <= $lastClosure->closed_until) {
            return response()->json([
                'success' => false,
                'message' => "La nouvelle date de clôture doit être postérieure à la dernière clôture enregistrée ({$lastClosure->closed_until->format('d-m-Y')}).",
            ], 422);
        }

        DB::beginTransaction();
        $validated['closed_by'] = $user->id;
        AccountingClosure::create($validated);
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "Vous avez clôturé la cession avec succès !",
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(AccountingClosure $accountingClosure)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccountingClosure $accountingClosure)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountingClosure $accountingClosure)
    {
        //
    }
}
