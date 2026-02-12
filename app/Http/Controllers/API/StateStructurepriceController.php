<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StateStructureprice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StateStructurepriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        abort_if(!in_array($user->user_role, ['etatique']), 403, "No permission");

        $structureprices = StateStructureprice::query();

        return DataTables::of($structureprices)
            ->addIndexColumn()
            ->editColumn('from', function ($row) {
                return $row->from->format('d-m-Y');
            })
            ->editColumn('to', function ($row) {
                return $row->to?->format('d-m-Y') ?? '-';
            })
            ->addColumn('tx', function ($row) {
                return "<span>1 USD = $row->usd_cdf CDF</span>";
            })
            ->addColumn('view', function ($row) use ($user) {
                $href = route('state.str-price', ['stx' => $row->id]);
                $t = "<a class='btn btn-sm btn-primary' href='$href'>
                        <i class='material-icons md-14 align-middle'>settings</i>
                        <span class='align-middle'>Voies et Structures</span>
                    </a>";
                return $t;
            })
            ->addColumn('action', function ($row) use ($user) {
                $data = e(json_encode([
                    'id' => $row->id,
                    'name' => $row->name,
                    'from' => $row->from->format('Y-m-d'),
                    'to' => $row->to?->format('Y-m-d'),
                    'usd_cdf' => $row->usd_cdf,
                ]));

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
                            <a class='dropdown-item' href='#' bedit data='$data'>
                                <i class='material-icons md-14 align-middle'>edit</i>
                                <span class='align-middle'>Modifier</span>
                            </a>
                            <a class="dropdown-item text-danger" href="#" bdel data='$data'>
                                <i class="material-icons md-14 align-middle">delete</i>
                                <span class="align-middle">Supprimer</span>
                            </a>
                        </div>
                    </div>
                DATA;

                // if ($user->user_role == 'petrolier') {
                return $t;
                // }
            })
            ->rawColumns(['action', 'view', 'tx'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless(in_array($user->user_role, ['etatique']), 403, "No permission");

        if (request('action') == 'update') {
            $validated = $request->validate([
                'from' => 'nullable|string|date|before_or_equal:today',
                'to' => 'nullable|string|date|after_or_equal:from|before_or_equal:today',
                'usd_cdf' => 'required|numeric|min:0.00000001',
            ], [
                'from.required' => 'Veuillez renseigner la Date validité initiale.',
                'from.before_or_equal' => 'La date de début ne peut pas être supérieure à la date actuelle.',
                'to.after_or_equal' => 'La date de fin doit être postérieure à la date de début.',
            ]);

            $user = auth()->user();
            $id = request('id');
            $str = StateStructureprice::findOrFail($id);

            if ($str->to) {
                DB::beginTransaction();
                $str->update(['usd_cdf' => request('usd_cdf')]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "La structure des prix a été mise à jour avec succès.",
                ], 200);
            }

            $strActif = StateStructureprice::whereNull('to')
                ->where('id', '!=', $str->id)
                ->first();

            if ($strActif) {
                return response()->json([
                    'success' => false,
                    'message' => "Une autre structure active existe déjà (valide depuis le {$strActif->from->format('d-m-Y')}). Veuillez d’abord la clôturer.",
                ], 422);
            }

            $dernierStr = StateStructureprice::whereNotNull('to')
                ->orderByDesc('to')
                ->first();

            if ($dernierStr && $dernierStr->to) {
                $dateAttendue = Carbon::parse($dernierStr->to)->addDay()->toDateString();
                if ($validated['from'] !== $dateAttendue) {
                    return response()->json([
                        'success' => false,
                        'message' => "La date de début doit être le lendemain de la dernière date de fin ({$dernierStr->to->format('d-m-Y')}) de la structure en cours.",
                    ], 422);
                }
            }

            DB::beginTransaction();
            $str->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "La structure des prix a été mise à jour avec succès.",
            ], 200);
        } else {
            $validated = $request->validate([
                'from' => 'required|string|date|before_or_equal:today',
                'usd_cdf' => 'required|numeric|min:0.00000001',
            ], [
                'from.required' => 'Veuillez renseigner la date de début.',
                'from.before_or_equal' => 'La date de début ne peut pas être supérieure à la date actuelle.',
            ]);


            $structureprices = new StateStructureprice;

            DB::beginTransaction();

            $lastStructure = $structureprices
                ->orderByDesc('from')
                ->first();

            $from = Carbon::parse($validated['from']);

            if ($lastStructure) {
                if (is_null($lastStructure->to)) {
                    $minStartDate = Carbon::parse($lastStructure->from)->addDays(2);
                    abort_if($from->lt($minStartDate), 422, "La nouvelle structure doit commencer au moins le {$minStartDate->format('d-m-Y')}.");

                    // Fermeture automatique de l’ancienne structure (n-1)
                    $lastStructure->update([
                        'to' => $from->copy()->subDay()->toDateString(),
                    ]);
                } else {
                    $expectedDate = Carbon::parse($lastStructure->to)->addDay();
                    abort_if($from->toDateString() !== $expectedDate->toDateString(), 422, "La nouvelle structure doit commencer le {$expectedDate->format('d-m-Y')}.");
                }
            }

            $st = $structureprices->create($validated);
            $name = strname(null, $st, true);
            $st->update(['name' => $name]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Vous avez créé la structure avec succès, veuillez maintenant configurer les prix de chaque carburant.",
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StateStructureprice $statestructureprice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StateStructureprice $statestructureprice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StateStructureprice $statestructureprice)
    {
        $user = auth()->user();
        abort_if(!in_array($user->user_role, ['etatique']), 403, "No permission");

        $last = StateStructureprice::orderByDesc('from')->first();
        if ($last->id !== $statestructureprice->id) {
            return response()->json(['success' => false, 'message' => "Vous ne pouvez pas supprimer une structure de prix du milieu, commencer par supprimer les dernières structures jusqu'à celle-ci",], 422);
        }

        $statestructureprice->delete();

        return response()->json([
            'success' => true,
            'message' => "Vous avez supprimé la structure $statestructureprice->name avec succès !",
        ], 200);
    }
}
