<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Structureprice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class StructurepriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        can('Structure des prix - Lire', true);

        $user = request()->user();

        if (isPetroUser() || isLogUser()) {
            $entity = gentity();
        } elseif (isEtaUser()) {
            $entity  = Entity::findOrFail(request('entity_id'));
        } else {
            abort(403);
        }
        abort_if(!$entity, 422, "No entity");
        $structureprices = $entity->structureprices();

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
            ->addColumn('view', function ($row) use ($user, $entity) {
                $param = ['stx' => $row->id];
                if (isPetroUser()) {
                    $href = route('provider.accounting', $param);
                } elseif (isLogUser()) {
                    $href = route('logistics.accounting', $param);
                } elseif (isEtaUser()) {
                    $href = state_route('accounting', array_merge(['entity' => $entity->id], $param));
                } else {
                    $href = route('sudo.provider', $param);
                }
                $t = "";
                if (can('Structure des prix - Modifier')) {
                    $t = "<a class='btn btn-sm btn-primary' href='$href'>
                        <i class='material-icons md-14 align-middle'>settings</i>
                        <span class='align-middle'>Voies et Structures</span>
                    </a>";
                }

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

                $btn = "";
                $btn1 = "
                    <a class='dropdown-item' href='#' bedit data='$data'>
                        <i class='material-icons md-14 align-middle'>edit</i>
                        <span class='align-middle'>Modifier</span>
                    </a>
                ";
                $btn2 = "
                        <a class='dropdown-item text-danger' href='#' bdel data='$data'>
                            <i class='material-icons md-14 align-middle'>delete</i>
                            <span class='align-middle'>Supprimer</span>
                        </a>";

                if (can('Structure des prix - Modifier')) {
                    $btn .= $btn1;
                }
                if (can('Structure des prix - Supprimer')) {
                    $btn .= $btn2;
                }

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
                            $btn
                        </div>
                    </div>
                DATA;

                if (empty($btn)) {
                    $t = '';
                }

                return $t;

            })
            ->rawColumns(['action', 'view', 'tx'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = request()->user();

        if (request('action') == 'update') {
            can('Structure des prix - Modifier', true);

            $validated = $request->validate([
                'from' => 'nullable|string|date|before_or_equal:today',
                'to' => 'nullable|string|date|after_or_equal:from|before_or_equal:today',
                'usd_cdf' => 'required|numeric|min:0.00000001',
            ], [
                'from.required' => 'Veuillez renseigner la Date validité initiale.',
                'from.before_or_equal' => 'La date de début ne peut pas être supérieure à la date actuelle.',
                'to.after_or_equal' => 'La date de fin doit être postérieure à la date de début.',
            ]);

            $user = request()->user();
            $id = request('id');
            $str = Structureprice::findOrFail($id);
            $entity = $str->entity;

            if (isPetroUser() || isLogUser()) {
                $parent = $user->user;
                if ($parent) {
                    $user = $parent;
                }
                abort_if($entity->users_id != $user->id, 403, "No permission !!!");
            } elseif (isEtaUser()) {
                //
            } else {
                abort(403, "No permission");
            }

            if ($str->to) {
                DB::beginTransaction();
                $str->update(['usd_cdf' => request('usd_cdf')]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "La structure des prix a été mise à jour avec succès.",
                ], 200);
            }

            $strActif = $entity->structureprices()
                ->whereNull('to')
                ->where('id', '!=', $str->id)
                ->first();

            if ($strActif) {
                return response()->json([
                    'success' => false,
                    'message' => "Une autre structure active existe déjà (valide depuis le {$strActif->from->format('d-m-Y')}). Veuillez d’abord la clôturer.",
                ], 422);
            }

            $dernierStr = $entity->structureprices()
                ->whereNotNull('to')
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
            can('Structure des prix - Créer', true);

            $validated = $request->validate([
                'from' => 'required|string|date|before_or_equal:today',
                'usd_cdf' => 'required|numeric|min:0.00000001',
            ], [
                'from.required' => 'Veuillez renseigner la date de début.',
                'from.before_or_equal' => 'La date de début ne peut pas être supérieure à la date actuelle.',
            ]);

            if (isPetroUser() || isLogUser()) {
                $entity = gentity();
            } elseif (isEtaUser()) {
                $entity  = Entity::findOrFail(request('entity_id'));
            } else {
                abort(403, "No permission");
            }

            abort_if(!$entity, 422, "No entity");
            $structureprices = $entity->structureprices();

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
            $name = strname($entity, $st);
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
    public function show(Structureprice $structureprice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Structureprice $structureprice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Structureprice $structureprice)
    {
        can('Structure des prix - Supprimer', true);

        $user = request()->user();
        abort_if(!isProLogEtaUser(), 403, "No permission");
        if (isEtaUser()) {
            //
        } else {
            $parent = $user->user;
            if ($parent) {
                $user = $parent;
            }
            abort_if($structureprice->entity->users_id != $user->id, 403, "Not permit");
        }

        $last = $structureprice->entity->structureprices()->orderByDesc('from')->first();
        if ($last->id !== $structureprice->id) {
            return response()->json(['success' => false, 'message' => "Vous ne pouvez pas supprimer une structure de prix du milieu, commencer par supprimer les dernières structures jusqu'à celle-ci",], 422);
        }

        $structureprice->delete();

        return response()->json([
            'success' => true,
            'message' => "Vous avez supprimé la structure $structureprice->name avec succès !",
        ], 200);
    }
}
