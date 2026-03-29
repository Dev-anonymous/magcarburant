<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Sale;
use App\Models\Salefile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        can('Vente - Lire', true);
        dd(2);

        if (isPetroUser() || isLogUser()) {
            $entity = gentity();
        } else if (isEtaUser()) {
            $entity  = Entity::findOrFail(request('entity_id'));
        } else {
            abort(403);
        }
        abort_if(!$entity, 422, "No entity");

        $date = request('date');
        $date = explode(' to ', $date);
        $date = array_filter($date);
        $from = @$date[0] ?? nnow()->toDateString();
        $to = @$date[1] ?? $from;
        $from_mutuality = request('from_mutuality');

        $sales = $entity->sales()->whereBetween('date', [$from, $to]);
        $sales->where('from_state', from_state());

        if ($from_mutuality === "1") {
            $sales->where('from_mutuality', 1);
        }
        if ($from_mutuality === "0") {
            $sales->where('from_mutuality', 0);
        }

        return DataTables::of($sales)
            ->addIndexColumn()
            ->addColumn('selall', function ($row) {
                return "
                <div class='custom-control custom-checkbox mt-3'>
                    <input type='checkbox' value='$row->id' id='id$row->id' class='selall custom-control-input'>
                    <label class='custom-control-label' for='id$row->id'>
                    </label>
                </div>
                ";
            })
            ->editColumn('date', function ($row) {
                return $row->date?->format('d-m-Y');
            })->editColumn('salefile', function ($row) {
                if ($row->sale) {
                    $sf = $row->sale->salefiles; // parent
                } else {
                    $sf = $row->salefiles;
                }
                $f = '';
                foreach ($sf as $i => $e) {
                    $u = asset('storage/' . $e->file);
                    $f .= "<a href='$u' class='text-nowrap mr-2' target='_blank'><i class='material-icons md-18 align-middle mb-1 text-primary'>attach_file</i> Fichier " . ($i + 1) . "</a>";
                }
                return "<div class=''>$f</div>";
            })
            ->addColumn('action', function ($row) {
                $d = $row->toArray();
                $d['date'] = $row->date?->format('Y-m-d');
                $data = e(json_encode($d));

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

                if (can('Vente - Modifier')) {
                    $btn .= $btn1;
                }
                if (can('Vente - Supprimer')) {
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

                if ($row->from_mutuality) {
                    $t = '';
                }

                if (isProLogEtaUser()) {
                    return $t;
                }
            })
            ->rawColumns(['action', 'salefile', 'selall'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = request()->user();

        if (request('action') == 'update') {
            can('Vente - Modifier', true);

            abort_if(!isProLogEtaUser(), 403, "No permission");

            $id = request('id');
            $sale = Sale::findOrFail($id);

            $validated = $request->validate(
                [
                    'date' => 'required|string|date|before_or_equal:today',
                    'terminal'  => 'required|string|in:' . implode(',', terminal()),
                    'product' => 'required|string|in:' . implode(',', mainfuels()),
                    'way' => 'required|string|in:' . implode(',', mainWays()),
                    'client'  => 'required|string|max:128',
                    'locality'  => 'required|string',
                    'delivery_note'  => 'required|string',
                    'delivery_program'  => 'required|string',
                    'lata'  => 'required|numeric|min:0',
                    'l15'  => 'required|numeric|min:0',
                    'density'  => 'required|numeric|min:0.001',
                    'salefile' => 'nullable|array',
                    'salefile.*' => 'mimes:pdf|max:10240'
                ],
                [
                    'terminal.in' => "Le terminal doit être l'un de ces éléments : " . implode(', ', terminal()),
                    'product.in' => "Le produit doit être l'un de ces éléments : " . implode(', ', mainfuels()),
                    'way.in' => "La voie doit être l'un de ces éléments : " . implode(', ', mainWays()),
                ]
            );

            abort_if('JET' == request('product') && 'NORD' == request('way'), 422, "Le JET n'est vendu qu'au SUD, EST et OUEST");

            DB::beginTransaction();

            $sale->update($validated);
            foreach ($sale->sales as $ch) { // children
                $ch->update($validated);
            }

            if ($request->hasFile('salefile')) {
                $insertFiles = [];
                foreach ($request->file('salefile') as $file) {
                    $insertFiles[] = [
                        'sale_id' => $sale->id,
                        'file' => $file->store('bills', 'public')
                    ];
                }

                if (!empty($insertFiles)) {
                    foreach ($sale->salefiles as $f) {
                        File::delete("storage/" . $f->file);
                        $f->delete();
                    }
                    foreach ($insertFiles as $i) {
                        Salefile::create($i);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Votre vente a été mise à jour avec succès !",
            ]);
        } elseif (request('action') == 'import') {
            can('Vente - Créer', true);

            $validated = $request->validate([
                'file' => 'required|file|mimes:xlsx,xls'
            ]);

            if (isPetroUser() || isLogUser()) {
                $entity = gentity();
            } else if (isEtaUser()) {
                $entity  = Entity::findOrFail(request('entity_id'));
            } else {
                abort(403);
            }

            $rows = Excel::toArray([], $request->file('file'));
            $sheet = (array) @$rows[0]; // première feuille EXCEL
            $sheet = array_slice($sheet, 1);
            $insert = [];
            $errors = [];

            DB::beginTransaction();
            foreach ($sheet as $index => $row) {
                $rowNumber = $index + 2;
                $colA = trim($row[0] ?? null);
                $colB = trim($row[1] ?? null);
                $colC = trim($row[2] ?? null);
                $colD = trim($row[3] ?? null);
                $colE = trim($row[4] ?? null);
                $colF = trim($row[5] ?? null);
                $colG = trim($row[6] ?? null);
                $colH = trim($row[7] ?? null);
                $colI = trim($row[8] ?? null);
                $colJ = trim($row[9] ?? null);
                $colK = trim($row[10] ?? null);

                if (empty(array_filter([$colA, $colB, $colC, $colD, $colE, $colF, $colG, $colH, $colI, $colJ, $colK]))) {
                    continue;
                }

                $lineErrors = [];

                // === A : Date ===
                if (empty($colA)) {
                    $lineErrors[] = "Cellule A$rowNumber : veuillez renseigner la date de la vente";
                }

                try {
                    if (is_numeric($colA)) {
                        $colA = Date::excelToDateTimeObject($colA)->format('Y-m-d');
                    } else {
                        $colA = Carbon::parse(str_replace('/', '-', $colA))->format('Y-m-d');
                    }

                    $date = Carbon::parse($colA);
                    if ($date->gt(Carbon::today())) {
                        $lineErrors[] =
                            "Cellule A$rowNumber : la date de la vente {$date->format('d-m-Y')} ne doit pas être > aujourd'hui";
                    }
                } catch (\Throwable $th) {
                    $lineErrors[] = "Cellule A$rowNumber : la date est invalide ou au mauvais format";
                }

                // === B : Terminal ===
                if (empty($colB)) {
                    $lineErrors[] = "Cellule B$rowNumber : veuillez renseigner le terminal";
                } else {
                    $colB = strtoupper($colB);
                    if (!in_array($colB, terminal(), true)) {
                        $lineErrors[] = "Cellule B$rowNumber : le terminal \"$colB\" n'est pas valide, les terminaux valides sont : " . implode(', ', terminal());
                    }
                }

                // === C : Localité ===
                if (empty($colC)) {
                    $lineErrors[] = "Cellule C$rowNumber : veuillez renseigner la localité";
                }

                // === D : Voie ===
                if (empty($colD)) {
                    $lineErrors[] = "Cellule D$rowNumber : veuillez renseigner la voie";
                } elseif (!in_array($colD, mainWays(), true)) {
                    $lineErrors[] = "Cellule D$rowNumber : la voie \"$colD\" n'est pas valide, les voies valides sont : " . implode(', ', mainWays());
                }

                // === E : Produit ===
                if (empty($colE)) {
                    $lineErrors[] = "Cellule E$rowNumber : veuillez renseigner le nom du produit";
                } elseif (!in_array($colE, mainfuels(), true)) {
                    $lineErrors[] = "Cellule E$rowNumber : le produit \"$colE\" n'est pas reconnu";
                }

                // === F : Bon de livraison ===
                if (empty($colF)) {
                    $lineErrors[] = "Cellule F$rowNumber : veuillez renseigner le bon de livraison";
                }

                // === G : Programme de livraison ===
                if (empty($colG)) {
                    $lineErrors[] = "Cellule G$rowNumber : veuillez renseigner le programme de livraison";
                }

                // === H : Client ===
                if (empty($colH)) {
                    $lineErrors[] = "Cellule H$rowNumber : veuillez renseigner le nom du client";
                }

                // === I, J, K : valeurs numériques ===
                foreach (
                    [
                        'I' => $colI,
                        'J' => $colJ,
                        'K' => $colK
                    ] as $colName => $value
                ) {
                    if (!is_numeric($value) || $value < 0) {
                        $lineErrors[] = "Cellule {$colName}{$rowNumber} : la valeur doit être un nombre >= 0";
                    }
                }

                if ('JET' == $colE && 'NORD' == $colD) {
                    $lineErrors[] = "Cellule E$rowNumber : le JET n'est vendu qu'au SUD, EST et OUEST";
                }
                // === Gestion des erreurs ===
                if (!empty($lineErrors)) {
                    $errors[] = implode('; ', $lineErrors);
                    continue;
                }

                if (isPetroUser() || isLogUser()) {
                    if (Sale::where([
                        'way' => $colD,
                        'product' => $colE,
                        'delivery_note' => $colF,
                        'delivery_program' => $colG,
                        'entity_id' => $entity->id,
                    ])->exists()) {
                        $errors[] = "La ligne $rowNumber(bon de livraison, programme de livraison) existe déjà dans l'application.";
                        continue;
                    }
                } /// uhm

                $ins = [
                    'entity_id'        => $entity->id,
                    'date'             => $colA,
                    'terminal'         => $colB,
                    'locality'         => $colC,
                    'way'              => $colD,
                    'product'          => $colE,
                    'delivery_note'    => $colF,
                    'delivery_program' => $colG,
                    'client'           => $colH,
                    'lata'             => $colI,
                    'l15'              => $colJ,
                    'density'          => $colK,
                    'from_state' => from_state(),
                ];
                $sale = Sale::create($ins);
                $insert[] = $ins;

                if (isEtaUser()) {
                    $logTerm = Entity::where('shortname', $colB)->first();
                    abort_if(!$logTerm, 422, "Le terminal spécifié ($colB) n'existe pas dans la base de données."); // uhm
                    $ins2 = $ins;
                    $ins2['entity_id'] = $logTerm->id;
                    Sale::create($ins2);
                }

                if (!isEtaUser()) {
                    if (strtoupper($colD) == 'OUEST') {
                        if ($entity->user->user_role == 'logisticien') {
                            $wz = $entity->workingzones()->with('zone')->get()->pluck('zone.zone')->all();
                            if (in_array($colD, $wz)) {
                                $entities = Entity::whereIn('users_id', User::where('user_role', 'logisticien')->where('id', '!=', $entity->user->id)->pluck('id')->all())->get();
                                foreach ($entities as $ent) {
                                    $ewz = $ent->workingzones()->with('zone')->get()->pluck('zone.zone')->all();
                                    if (in_array('OUEST', $ewz)) {
                                        $product = $colE;
                                        if (canmutuality($ent, $product)) {
                                            $ins['parent_id'] = $sale->id;
                                            $ins['from_mutuality'] = 1;
                                            $ins['entity_id'] = $ent->id;
                                            Sale::create($ins);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (count($errors)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => implode('<br/>', $errors),
                ], 422);
            }

            if (!count($insert)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Aucune ligne n'a été importée. Veuillez remplir le fichier excel en commençant par la cellule A2.",
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Votre fichier a été importé avec succès.",
            ], 201);
        } else {
            can('Vente - Créer', true);

            if (isPetroUser() || isLogUser()) {
                $entity = gentity();
            } elseif (isEtaUser()) {
                $entity  = Entity::findOrFail(request('entity_id'));
            } else {
                abort(403);
            }
            abort_if(!$entity, 422, "No entity");

            $validated = $request->validate(
                [
                    'date' => 'required|string|date|before_or_equal:today',
                    'terminal'  => 'required|string|in:' . implode(',', terminal()),
                    'product' => 'required|string|in:' . implode(',', mainfuels()),
                    'way' => 'required|string|in:' . implode(',', mainWays()),
                    'client'  => 'required|string|max:128',
                    'locality'  => 'required|string',
                    'delivery_note'  => 'required|string',
                    'delivery_program'  => 'required|string',
                    'lata'  => 'required|numeric|min:0',
                    'l15'  => 'required|numeric|min:0',
                    'density'  => 'required|numeric|min:0.001',
                    'salefile' => 'nullable|array',
                    'salefile.*' => 'mimes:pdf|max:10240'
                ],
                [
                    'terminal.in' => "Le terminal doit être l'un de ces éléments : " . implode(', ', terminal()),
                    'product.in' => "Le produit doit être l'un de ces éléments : " . implode(', ', mainfuels()),
                    'way.in' => "La voie doit être l'un de ces éléments : " . implode(', ', mainWays()),
                ]
            );

            abort_if('JET' == request('product') && 'NORD' == request('way'), 422, "Le JET n'est vendu qu'au SUD, EST et OUEST");

            DB::beginTransaction();
            $validated['entity_id'] = $entity->id;
            $validated['from_state'] = from_state();

            $sale = Sale::create($validated);

            if (isEtaUser() && $entity->user->user_role === 'petrolier') {
                $logTerm = Entity::where('shortname', $validated['terminal'])->first();
                abort_if(!$logTerm, 422, "Le terminal spécifié (" . $validated['terminal'] . ") n'existe pas dans la base de données."); // uhm
                $validated2 = $validated;
                $validated2['entity_id'] = $logTerm->id;
                Sale::create($validated2);
            }

            if ($entity->user->user_role == 'logisticien') {
                if (!isEtaUser()) {
                    $wz = $entity->workingzones()->with('zone')->get()->pluck('zone.zone')->all();
                    $w = strtoupper(request('way'));
                    if (in_array($w, $wz) && $w == 'OUEST') {
                        $entities = Entity::whereIn('users_id', User::where('user_role', 'logisticien')->where('id', '!=', $entity->user->id)->pluck('id')->all())->get();
                        foreach ($entities as $ent) {
                            $ewz = $ent->workingzones()->with('zone')->get()->pluck('zone.zone')->all();
                            if (in_array('OUEST', $ewz)) {
                                $product = $validated['product'];
                                if (canmutuality($ent, $product)) {
                                    $validated['parent_id'] = $sale->id;
                                    $validated['from_mutuality'] = 1;
                                    $validated['entity_id'] = $ent->id;
                                    Sale::create($validated);
                                }
                            }
                        }
                    }
                }
            }

            $f = [];
            if ($request->hasFile('salefile')) {
                foreach ($request->file('salefile') as $file) {
                    $f[] = ['sale_id' => $sale->id, 'file' =>  $file->store('bills', 'public')];
                }
            }
            foreach ($f as $i) {
                Salefile::create($i);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Votre vente a été enregistrée avec succès !",
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        can('Vente - Supprimer', true);

        abort_if(!isProLogEtaUser(), 403, "No permission");
        if (isEtaUser()) {
            //
        } else {
            $entity = gentity();
            abort_if($entity->id != $sale->entity_id, 403, "Not permit");
        }

        if ('bulk' == request('action')) {
            $ids = (array) json_decode(request('ids'));
            $sales = Sale::whereIn('id', $ids)->get();
            DB::beginTransaction();
            foreach ($sales as $sale) {
                foreach ($sale->sales as $s) {
                    foreach ($s->salefiles as $f) {
                        File::delete("storage/" . $f->file);
                    }
                    $s->delete();
                }
                foreach ($sale->salefiles as $f) {
                    File::delete("storage/" . $f->file);
                }
                $sale->delete();
            }
            DB::commit();
            $n = count($ids);

            return response()->json([
                'success' => true,
                'message' => "Vous avez supprimé $n vente(s) avec succès !",
            ], 200);
        }

        DB::beginTransaction();
        foreach ($sale->sales as $s) {
            foreach ($s->salefiles as $f) {
                File::delete("storage/" . $f->file);
            }
            $s->delete();
        }

        foreach ($sale->salefiles as $f) {
            File::delete("storage/" . $f->file);
        }
        $sale->delete();
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "Vous avez supprimé la vente #$sale->id avec succès !",
        ], 200);
    }
}
