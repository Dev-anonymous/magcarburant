<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Structureprice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class Structureprices extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        abort_if(!in_array($user->user_role, ['sudo', 'provider']), 403, "No permission");

        if ($user->user_role == 'provider') {
            $entity = $user->entities()->first();
        } else {
            $entity = Entity::find(request('entity_id'));
        }
        abort_if(!$entity, 422, "No entity");
        $structureprices = $entity->structureprices;

        return DataTables::of($structureprices)
            ->addIndexColumn()
            ->editColumn('from', function ($row) {
                return $row->from->format('d-m-Y');
            })
            ->editColumn('to', function ($row) {
                return $row->to?->format('d-m-Y') ?? '-';
            })
            ->addColumn('view', function ($row) use ($user) {
                if ($user->user_role == 'provider') {
                    $href = route('provider.prices', ['st' => $row->id]);
                } else {
                    $href = route('sudo.provider', ['st' => $row->id]);
                }
                $t = "<a class='dropdown-item' href='$href'>
                        <i class='material-icons md-14 align-middle'>settings</i>
                        <span class='align-middle'>Voies et Structures</span>
                    </a>";
                return $t;
            })
            ->addColumn('action', function ($row) use ($user) {
                $eb = "";
                $data = e(json_encode([
                    'id' => $row->id,
                    'name' => $row->name,
                    'from' => $row->from->format('Y-m-d'),
                    'to' => $row->to?->format('Y-m-d'),
                    'cdf_usd' => $row->cdf_usd,
                    'usd_cdf' => $row->usd_cdf,
                ]));
                if (!$row->to) {
                    $eb = "
                        <a class='dropdown-item' href='#' bedit data='$data'>
                            <i class='material-icons md-14 align-middle'>edit</i>
                            <span class='align-middle'>Modifier</span>
                        </a>
                    ";
                }
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
                            $eb
                            <a class="dropdown-item text-danger" href="#" bdel data='$data'>
                                <i class="material-icons md-14 align-middle">delete</i>
                                <span class="align-middle">Supprimer</span>
                            </a>
                        </div>
                    </div>
                DATA;

                if ($user->user_role == 'provider') {
                    return $t;
                }
            })
            ->rawColumns(['action', 'view'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_if($user->user_role !== 'provider', 403, "No permission");

        if (request('action') == 'update') {
            $validated = $request->validate([
                'from' => 'required|string|date|before_or_equal:today',
                'to' => 'nullable|string|date|after_or_equal:from|before_or_equal:today',
            ], [
                'from.required' => 'Veuillez renseigner la Date validité initiale.',
                'from.before_or_equal' => 'La date de début ne peut pas être supérieure à la date actuelle.',
                'to.after_or_equal' => 'La date de fin doit être postérieure à la date de début.',
            ]);

            $user = auth()->user();
            $id = request('id');
            $str = Structureprice::findOrFail($id);
            $entity = $str->entity;
            abort_if($entity->users_id != $user->id, 403, "No permission !!!");

            if ($str->to) {
                return response()->json([
                    'success' => false,
                    'message' => "Cette structure est clôturée (valide du {$str->from->format('d-m-Y')} au {$str->to->format('d-m-Y')}) et ne peut plus être modifiée.",
                ], 422);
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

            if (request('to')) {
                //
                // dd('?????');
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
            ], [
                'from.required' => 'Veuillez renseigner la date de début.',
                'from.before_or_equal' => 'La date de début ne peut pas être supérieure à la date actuelle.',
            ]);

            $entity = $user->entities()->first();
            abort_if(!$entity, 422, "No entity");
            $structureprices = $entity->structureprices();
            $strActif = $structureprices->whereNull('to')->first();

            if ($strActif) {
                return response()->json([
                    'success' => false,
                    'message' => "Impossible d’ajouter une nouvelle structure des prix : Vous devez renseigner la date de validité fin de la structure des prix en cours : du {$strActif->from?->format('d-m-Y')}  au ... ? .",
                ], 422);
            }

            $dernierStr = $entity->structureprices()->orderByDesc('to')->first();

            if ($dernierStr && $dernierStr->to) {
                $dateAttendue = Carbon::parse($dernierStr->to)->addDay();
                if ($validated['from'] !== $dateAttendue->toDateString()) {
                    return response()->json([
                        'success' => false,
                        'message' => "La date fin de la dernière structure des prix est {$dernierStr->to->format('d-m-Y')}, la date de debut de la nouvelle structure des prix doit être : {$dateAttendue->format('d-m-Y')}.",
                    ], 422);
                }
            }

            DB::beginTransaction();
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
        $user = auth()->user();
        abort_if($structureprice->entity->users_id != $user->id, 403, "Not permit");

        if ($structureprice->to) {
            $str = Structureprice::where('from', '>', $structureprice->to)->where('entity_id', $structureprice->entity->id)->first();
            if ($str) {
                return response()->json([
                    'success' => false,
                    'message' => "Vous ne pouvez pas supprimer une structure de prix du milieu, commencer par supprimer les dernières structures jusqu'à celle-ci.",
                ], 422);
            }
        }

        $structureprice->delete();

        return response()->json([
            'success' => true,
            'message' => "Vous avez supprimé la structure $structureprice->name avec succès !",
        ], 200);
    }
}
