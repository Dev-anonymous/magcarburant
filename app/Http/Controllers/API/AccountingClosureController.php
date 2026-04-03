<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AccountingClosure;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AccountingClosureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        can('Configuration - Lire', true);

        $user = request()->user();
        abort_if(!isEtaUser(), 403, "No permission");

        $entity = Entity::findOrFail(request('entity_id'));

        $data = AccountingClosure::orderByDesc('id')->where('entity_id', $entity->id);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('closed_until', function ($row) {
                return $row->closed_until?->format('d-m-Y');
            })->editColumn('closed_by', function ($row) {
                return $row->user?->name;
            })->addColumn('action', function ($row) {
                $eb = "";
                $d = $row->toArray();
                $d['closed_until'] = $row->closed_until?->format('Y-m-d');
                $data = e(json_encode($d));
                $eb = "
                    <a class='dropdown-item' href='#' bedit data-data='$data'>
                        <i class='material-icons md-14 align-middle'>edit</i>
                        <span class='align-middle'>Modifier</span>
                    </a>
                ";
                $t = <<<DATA
                    <div class="dropdown">
                        <a
                            class="btn btn-primary2 btn-sm"
                            href="#"
                            role="button"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <i class="material-icons md-18 align-middle"
                            >more_vert</i
                            >
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            $eb
                        </div>
                    </div>
                DATA;

                if (!can('Configuration - Modifier')) return;

                return $t;
            })->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        can('Configuration - Modifier', true);

        $user = request()->user();
        abort_if(!isEtaUser(), 403, "No permission");

        $validated = $request->validate([
            'closed_until' => 'required|date|before_or_equal:today',
            'entity_id' => 'required|numeric|exists:entity,id',
        ], [
            'closed_until.required' => 'Veuillez renseigner la date de clôture de la cession.',
        ]);

        $lastClosure = AccountingClosure::where('entity_id', $validated['entity_id'])
            ->orderBy('closed_until', 'desc')
            ->first();

        if ($lastClosure && Carbon::parse($validated['closed_until'])->lte($lastClosure->closed_until)) {
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
    public function show(AccountingClosure $accountingclosure) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccountingClosure $accountingclosure)
    {
        $user = $request->user();
        abort_if(!in_array($user->user_role, ['etatique']), 403, "No permission");

        $validated = $request->validate([
            'closed_until' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $previousClosure = AccountingClosure::where('entity_id', $accountingclosure->entity_id)
            ->where('closed_until', '<', $accountingclosure->closed_until)
            ->orderBy('closed_until', 'desc')->first();
        $nextClosure = AccountingClosure::where('entity_id', $accountingclosure->entity_id)
            ->where('closed_until', '>', $accountingclosure->closed_until)
            ->orderBy('closed_until', 'asc')->first();

        $newDate = Carbon::parse($validated['closed_until']);

        // Vérifier bornes
        if ($previousClosure && $newDate->lte($previousClosure->closed_until)) {
            return response()->json([
                'success' => false,
                'message' => "La nouvelle date doit être postérieure à la clôture précédente ({$previousClosure->closed_until->format('d-m-Y')}).",
            ], 422);
        }

        if ($nextClosure && $newDate->gte($nextClosure->closed_until)) {
            return response()->json([
                'success' => false,
                'message' => "La nouvelle date doit être antérieure à la clôture suivante ({$nextClosure->closed_until->format('d-m-Y')}).",
            ], 422);
        }

        DB::transaction(function () use ($accountingclosure, $newDate, $user) {
            $accountingclosure->update([
                'closed_until' => $newDate,
                'closed_by' => $user->id,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "La clôture a été mise à jour avec succès !",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountingClosure $accountingclosure)
    {
        //
    }
}
