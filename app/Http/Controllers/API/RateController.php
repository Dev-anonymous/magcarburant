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
        if ($user->user_role === 'provider') {
            $entity = $user->entities()->first();
            abort_if(!$entity, 422, "No entity");
            $rates = $entity->rates;
        } else if ($user->user_role === 'sudo') {
            $entity = Entity::find(request('entity_id'));
            if (request('type') == 'structure') {
                // admin structure rate
                $rates = Rate::where('type', 'structure');
            } else {
                $rates = $entity->rates;
            }
        } else {
            abort(403);
        }

        return DataTables::of($rates)
            ->addIndexColumn()
            ->editColumn('from', function ($row) {
                return $row->from->format('d-m-Y');
            })
            ->editColumn('to', function ($row) {
                return $row->to?->format('d-m-Y') ?? '-';
            })
            ->addColumn('rate', function ($row) {
                $t = "<span>1 CDF = $row->cdf_usd USD</span><br/>";
                $t .= "<span>1 USD = $row->usd_cdf CDF</span>";
                return $t;
            })
            ->addColumn('action', function ($row) {
                if (!$row->to) {
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
                                class="btn btn-white btn-sm"
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
                                <a class="dropdown-item" href="#" bedit data='$data'>
                                    <i class="material-icons md-14 align-middle">edit</i>
                                    <span class="align-middle">Modifier</span>
                                </a>
                            </div>
                        </div>
                    DATA;
                    return $t;
                }
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
                'from' => 'required|string|date|before_or_equal:today',
                'to' => 'nullable|string|date|after_or_equal:from|before_or_equal:today',
                'cdf_usd' => 'required|numeric|min:0.00000001',
                'usd_cdf' => 'required|numeric|min:0.00000001',
            ], [
                'from.required' => 'Veuillez renseigner la Date validité initiale.',
                'from.before_or_equal' => 'La date de début ne peut pas être supérieure à la date actuelle.',
                'to.before_or_equal' => 'La date de fin ne peut pas être supérieure à la date actuelle.',
                'to.after_or_equal' => 'La date de fin doit être postérieure à la date de début.',
            ]);

            $user = auth()->user();
            $id = request('id');
            $rate = Rate::findOrFail($id);
            $entity = $rate->entity;
            abort_if($user->user_role == 'provider' && $entity->users_id != $user->id, 403, "No permission !!!");
            $isStTX = false;

            if ($user->user_role == 'provider') {
                //
            } else if ($user->user_role == 'sudo') {
                $isStTX = true;
                abort_if('STRUCTURE' !== $rate->type, 403, "invalid data");
                // $rate = Rate::where('type', 'STRUCTURE')->
            } else {
                abort(403);
            }

            if ($rate->to) {
                return response()->json([
                    'success' => false,
                    'message' => "Ce taux est clôturé (valide du {$rate->from->format('d-m-Y')} au {$rate->to->format('d-m-Y')}) et ne peut plus être modifié.",
                ], 422);
            }

            if ($isStTX) {
                $tauxActif = Rate::where('type', 'STRUCTURE')
                    ->whereNull('to')
                    ->where('id', '!=', $rate->id)
                    ->first();
            } else {
                $tauxActif = $entity->rates()
                    ->where('type', 'RÉEL')
                    ->whereNull('to')
                    ->where('id', '!=', $rate->id)
                    ->first();
            }

            if ($tauxActif) {
                return response()->json([
                    'success' => false,
                    'message' => "Un autre taux actif existe déjà (valide depuis le {$tauxActif->from->format('d-m-Y')}). Veuillez d’abord le clôturer.",
                ], 422);
            }

            if ($isStTX) {
                $dernierTaux = Rate::where('type', 'STRUCTURE')->whereNotNull('to')
                    ->orderByDesc('to')
                    ->first();
            } else {
                $dernierTaux = $entity->rates()
                    ->whereNotNull('to')
                    ->orderByDesc('to')
                    ->first();
            }

            if ($dernierTaux && $dernierTaux->to) {
                $dateAttendue = Carbon::parse($dernierTaux->to)->addDay()->toDateString();
                if ($validated['from'] !== $dateAttendue) {
                    return response()->json([
                        'success' => false,
                        'message' => "La date de début doit être le lendemain de la dernière date de fin ({$dernierTaux->to}).",
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
                'cdf_usd' => 'required|numeric|min:0.00000001',
                'usd_cdf' => 'required|numeric|min:0.00000001',
            ], [
                'from.required' => 'Veuillez renseigner la Date validité initiale.',
                'from.before_or_equal' => 'La date de début ne peut pas être supérieure à la date actuelle.',
                'to.after_or_equal' => 'La date de fin doit être postérieure à la date de début.',
            ]);

            $isStTX = false;
            $user = auth()->user();
            if ($user->user_role == 'provider') {
                $entity = $user->entities()->first();
                abort_if(!$entity, 422, "No entity");
                $rate = $entity->rates();
                $tauxActif = $rate->where('type', 'RÉEL')->whereNull('to')->first();
                $validated['type'] = 'RÉEL';
            } else if ($user->user_role == 'sudo') {
                $isStTX = true;
                $validated['type'] = 'STRUCTURE';
                $tauxActif = Rate::where('type', 'STRUCTURE')->whereNull('to')->first();
                $rate = new Rate;
            } else {
                abort(403, "No permission");
            }

            if ($tauxActif) {
                return response()->json([
                    'success' => false,
                    'message' => "Impossible d’ajouter un nouveau taux : Vous devez renseigner la date de validité fin du taux en cours : du {$tauxActif->from?->format('d-m-Y')}  au ... ? .",
                ], 422);
            }

            if ($isStTX) {
                $dernierTaux = Rate::where('type', 'STRUCTURE')->orderByDesc('to')->first();
            } else {
                $dernierTaux = $entity->rates()->orderByDesc('to')->first();
            }
            if ($dernierTaux && $dernierTaux->to) {
                $dateAttendue = Carbon::parse($dernierTaux->to)->addDay();
                if ($validated['from'] !== $dateAttendue->toDateString()) {
                    return response()->json([
                        'success' => false,
                        'message' => "La date fin du dernier taux est {$dernierTaux->to->format('d-m-Y')}, la date de debut du nouveau taux doit être : {$dateAttendue->format('d-m-Y')}.",
                    ], 422);
                }
            }

            DB::beginTransaction();
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
        //
    }
}
