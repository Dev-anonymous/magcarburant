<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Rate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $type = request('type');
        $isTx = true;
        if (in_array($user->user_role, ['petrolier', 'logisticien'])) {
            $entity = $user->entities()->first();
            abort_if(!$entity, 422, "No entity");
            if ($type == 'structure') {
                $data = $entity->structureprices();
                $isTx = false;
            } else {
                $data = $entity->rates();
            }
        } else if ($user->user_role === 'sudo') {
            // $entity = Entity::find(request('entity_id'));
            // if ($type == 'structure') {
            //     // admin structure rate
            //     $rates = Rate::where('type', 'structure');
            // } else {
            //     $rates = $entity->rates;
            // }
            abort(403);
        } else {
            abort(403);
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('from', function ($row) {
                return $row->from->format('d-m-Y');
            })
            ->editColumn('to', function ($row) {
                return $row->to?->format('d-m-Y') ?? '-';
            })
            ->addColumn('rate', function ($row) {
                return "<span>1 USD = $row->usd_cdf CDF</span>";
            })
            ->addColumn('action', function ($row) use ($user, $type, $isTx) {
                $data = e(json_encode([
                    'id' => $row->id,
                    'from' => $row->from->format('Y-m-d'),
                    'to' => $row->to?->format('Y-m-d'),
                    'cdf_usd' => $row->cdf_usd,
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

                return $t;
            })
            ->rawColumns(['action', 'rate'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (request('action') == 'update') {
            $validated = $request->validate([
                'from' => 'nullable|string|date|before_or_equal:today',
                'to' => 'nullable|string|date|after_or_equal:from|before_or_equal:today',
                'usd_cdf' => 'required|numeric|min:0.00000001',
            ], [
                'from.required' => 'Veuillez renseigner la date validité initiale.',
                'from.before_or_equal' => 'La date de début ne peut pas être supérieure à la date actuelle.',
                'to.before_or_equal' => 'La date de fin ne peut pas être supérieure à la date actuelle.',
                'to.after_or_equal' => 'La date de fin doit être postérieure à la date de début.',
            ]);

            $user = auth()->user();
            $id = request('id');
            $rate = Rate::findOrFail($id);
            $entity = $rate->entity;
            abort_if($user->user_role == 'petrolier' && $entity->users_id != $user->id, 403, "No permission !!!");

            if (in_array($user->user_role, ['petrolier', 'logisticien'])) {
                //
            } else {
                abort(403);
            }

            if ($rate->to) {
                DB::beginTransaction();
                $rate->update(['usd_cdf' => request('usd_cdf')]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Le taux a été mis à jour avec succès.",
                ], 200);
            }

            $tauxActif = $entity->rates()
                ->whereNull('to')
                ->where('id', '!=', $rate->id)
                ->first();

            if ($tauxActif) {
                return response()->json([
                    'success' => false,
                    'message' => "Un autre taux actif existe déjà (valide depuis le {$tauxActif->from->format('d-m-Y')}). Veuillez d’abord le clôturer.",
                ], 422);
            }

            $dernierTaux = $entity->rates()
                ->whereNotNull('to')
                ->orderByDesc('to')
                ->first();

            if ($dernierTaux && $dernierTaux->to) {
                $dateAttendue = Carbon::parse($dernierTaux->to)->addDay()->toDateString();
                if ($validated['from'] !== $dateAttendue) {
                    return response()->json([
                        'success' => false,
                        'message' => "La date de début doit être le lendemain de la dernière date de fin ({$dernierTaux->to->format('d-m-Y')}) du taux encours.",
                    ], 422);
                }
            }

            DB::beginTransaction();
            $rate->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Le taux a été mis à jour avec succès.",
            ], 200);
        } else {
            $validated = $request->validate([
                'from' => 'required|string|date|before_or_equal:today',
                'to' => 'nullable|string|date|after_or_equal:from|before_or_equal:today',
                'usd_cdf' => 'required|numeric|min:0.00000001',
            ], [
                'from.required' => 'Veuillez renseigner la date validité initiale.',
                'from.before_or_equal' => 'La date de début ne peut pas être supérieure à la date actuelle.',
                'to.after_or_equal' => 'La date de fin doit être postérieure à la date de début.',
            ]);

            $user = auth()->user();
            if (in_array($user->user_role, ['petrolier', 'logisticien'])) {
                $entity = $user->entities()->first();
                abort_if(!$entity, 422, "No entity");
                $rate = $entity->rates();
                $lastTx = $entity->rates()->orderByDesc('from')->first();
            } else {
                abort(403, "No permission");
            }

            $from = Carbon::parse($validated['from']);

            DB::beginTransaction();

            if ($lastTx) {
                if (is_null($lastTx->to)) {
                    $minStartDate = Carbon::parse($lastTx->from)->addDays(1);
                    abort_if($from->lt($minStartDate), 422, "Le nouveau taux doit commencer au moins le {$minStartDate->format('d-m-Y')}.");
                    $lastTx->update([
                        'to' => $from->copy()->subDay()->toDateString(),
                    ]);
                } else {
                    $expectedDate = Carbon::parse($lastTx->to)->addDay();
                    abort_if($from->toDateString() !== $expectedDate->toDateString(), 422, "La nouveaux taux doit commencer le {$expectedDate->format('d-m-Y')}.");
                }
            }

            $rate->create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Vous avez créé le taux avec succès !",
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Rate $rate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rate $rate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rate $rate)
    {
        $user = auth()->user();
        abort_if($rate->entity->users_id != $user->id, 403, "Not permit");

        $last = $rate->entity->rates()->orderByDesc('from')->first();
        if ($last->id !== $rate->id) {
            return response()->json(['success' => false, 'message' => "Vous ne pouvez pas supprimer un taux du milieu, commencer par supprimer les dernièrs taux jusqu'à celui-ci",], 422);
        }

        $rate->delete();

        return response()->json([
            'success' => true,
            'message' => "Vous avez supprimé le taux #$rate->id avec succès !",
        ], 200);
    }
}
