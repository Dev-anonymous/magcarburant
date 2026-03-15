<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Deliveryfile;
use App\Models\Entity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Yajra\DataTables\Facades\DataTables;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();

        abort_if(!in_array($user->user_role, ['petrolier', 'etatique']), 403, "No permission");

        if ($user->user_role == 'petrolier') {
            $entity = $user->entities()->first();
        } else if ($user->user_role == 'etatique') {
            $entity  = Entity::findOrFail(request('entity_id'));
        } else {
            abort(403);
        }
        abort_if(!$entity, 422, "No entity");
        $deliveries = $entity->deliveries();
        if (from_state()) {
            $deliveries->where('from_state', 1);
        } else {
            $deliveries->where('from_state', 0);
        }

        $date = request('date');
        $date = explode(' to ', $date);
        $date = array_filter($date);
        $from = @$date[0] ?? nnow()->toDateString();
        $to = @$date[1] ?? $from;
        $zones = (array) request('zones');
        $fuels = (array) request('fuels');

        $deliveries->whereBetween('date', [$from, $to])->whereIn('product', $fuels)->whereIn('way', $zones);

        return DataTables::of($deliveries)
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
            ->addColumn('total', function ($row) {
                $v = v(($row->lata) * $row->unitprice);
                return "<span title='LATA * Prix unitaire' tooltip>$v</span>";
            })->editColumn('date', function ($row) {
                return $row->date?->format('d-m-Y');
            })->editColumn('deliveryfile', function ($row) {
                $f = '';
                foreach ($row->deliveryfiles as $i => $e) {
                    $u = asset('storage/' . $e->file);
                    $f .= "<a href='$u' class='text-nowrap mr-2' target='_blank'><i class='material-icons md-18 align-middle mb-1 text-primary'>attach_file</i> Fichier " . ($i + 1) . "</a>";
                }
                return "<div class=''>$f</div>";
            })
            ->addColumn('action', function ($row) use ($user) {
                $eb = "";
                $d = $row->toArray();
                $d['date'] = $row->date?->format('Y-m-d');
                $data = e(json_encode($d));
                $eb = "
                    <a class='dropdown-item' href='#' bedit data='$data'>
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
                            <a class="dropdown-item text-danger" href="#" bdel data='$data'>
                                <i class="material-icons md-14 align-middle">delete</i>
                                <span class="align-middle">Supprimer</span>
                            </a>
                        </div>
                    </div>
                DATA;

                if (in_array($user->user_role, ['petrolier', 'etatique'])) {
                    return $t;
                }
            })
            ->rawColumns(['action', 'deliveryfile', 'total', 'selall'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = request()->user();

        if (request('action') == 'update') {
            $id = request('id');
            $delivery = Delivery::findOrFail($id);

            $validated = $request->validate([
                'date' => 'required|string|date|before_or_equal:today',
                'terminal'  => 'required|string|max:255',
                'product' => 'required|string|in:' . implode(',', mainfuels()),
                'way' => 'required|string|in:' . implode(',', mainWays()),
                'client'  => 'required|string|max:128',
                'locality'  => 'required|string',
                'delivery_note'  => 'required|string',
                'delivery_program'  => 'required|string',
                'lata'  => 'required|numeric|min:0.001',
                'unitprice'  => 'required|numeric|min:0.001',
                'deliveryfile' => 'nullable|array',
                'deliveryfile.*' => 'mimes:pdf|max:10240'
            ]);

            abort_if('JET' == request('product') && 'NORD' == request('way'), 422, "Le JET n'est vendu qu'au SUD, EST et OUEST");

            DB::beginTransaction();

            $delivery->update($validated);

            if ($request->hasFile('deliveryfile')) {
                $insertFiles = [];
                foreach ($request->file('deliveryfile') as $file) {
                    $insertFiles[] = [
                        'delivery_id' => $delivery->id,
                        'file' => $file->store('bills', 'public')
                    ];
                }

                if (!empty($insertFiles)) {
                    foreach ($delivery->deliveryfiles as $f) {
                        File::delete("storage/" . $f->file);
                        $f->delete();
                    }
                    foreach ($insertFiles as $f) {
                        Deliveryfile::create($f);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Votre livraison a été mise à jour avec succès !",
            ]);
        } elseif (request('action') == 'import') {
            $validated = $request->validate([
                'file' => 'required|file|mimes:xlsx,xls'
            ]);

            if (in_array($user->user_role, ['petrolier', 'logisticien'])) {
                $entity = $user->entities()->first();
            } else if ($user->user_role == 'etatique') {
                $entity  = Entity::findOrFail(request('entity_id'));
            } else {
                abort(403);
            }

            $rows = Excel::toArray([], $request->file('file'));
            $sheet = (array) @$rows[0]; // première feuille EXCEL
            $sheet = array_slice($sheet, 1);
            $insert = [];
            $errors = [];
            foreach ($sheet as $index => $row) {
                $rowNumber = $index + 2;
                $colA = trim($row[0] ?? null);
                $colB = trim($row[1] ?? null);
                $colC = trim($row[2] ?? null);
                $colD = strtoupper(trim($row[3] ?? null));
                $colE = strtoupper(trim($row[4] ?? null));
                $colF = trim($row[5] ?? null);
                $colG = trim($row[6] ?? null);
                $colH = trim($row[7] ?? null);
                $colI = trim($row[8] ?? null);
                $colJ = trim($row[9] ?? null);
                $row = array_splice($row, 0, 10);

                if (empty(array_filter($row))) {
                    continue;
                }

                $lineErrors = [];

                // === A : Date ===
                if (empty($colA)) {
                    $lineErrors[] = "Cellule A$rowNumber : veuillez renseigner la date de la vente";
                }

                try {
                    //  Date Excel (nombre)
                    if (is_numeric($colA)) {
                        $date = Carbon::instance(Date::excelToDateTimeObject($colA));
                    } else {
                        $value = trim($colA);

                        //  Formats texte acceptés
                        $formats = [
                            'Y-m-d',
                            'd/m/Y',   // format français
                            'd-m-Y',
                            'Y/m/d',
                        ];

                        $date = null;
                        foreach ($formats as $format) {
                            try {
                                $date = Carbon::createFromFormat($format, $value);
                                break;
                            } catch (\Exception $e) {
                            }
                        }

                        if (!$date) {
                            throw new \Exception('Format invalide');
                        }
                    }

                    // 3️⃣ Normalisation
                    $date = $date->startOfDay();

                    // 4️⃣ Validation métier
                    if ($date->gt(Carbon::today())) {
                        $lineErrors[] =
                            "Cellule A{$rowNumber} : la date {$date->format('d/m/Y')} ne doit pas être > aujourd'hui";
                    }

                    $colA = $date->toDateString();
                } catch (\Throwable $th) {
                    $lineErrors[] = "Cellule A{$rowNumber} : date invalide ou format non supporté";
                }

                // === B : Terminal ===
                if (empty($colB)) {
                    $lineErrors[] = "Cellule B$rowNumber : veuillez renseigner le terminal";
                }

                // === C : Localité ===
                if (empty($colC)) {
                    $lineErrors[] = "Cellule C$rowNumber : veuillez renseigner la localité";
                }

                // === D : Voie ===
                if (!in_array($colD, mainWays(), true)) {
                    $lineErrors[] = "Cellule D$rowNumber : la voie \"$colD\" n'est pas valide";
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
                } else {
                    if (Delivery::where(['delivery_note' => $colF, 'entity_id' => $entity->id, 'from_state' => from_state()])->exists()) {
                        $lineErrors[] = "Cellule F{$rowNumber} : le bon de livraison $colF existe déjà";
                    }
                }

                // === G : Programme de livraison ===
                if (empty($colG)) {
                    $lineErrors[] = "Cellule G$rowNumber : veuillez renseigner le programme de livraison";
                }

                // === H : Client ===
                if (empty($colH)) {
                    $lineErrors[] = "Cellule H$rowNumber : veuillez renseigner le nom du client";
                }

                // === I, J: valeurs numériques ===
                foreach (
                    [
                        'I' => $colI,
                        'J' => $colJ,
                    ] as $colName => $value
                ) {
                    if (!is_numeric($value) || $value < 0.001) {
                        $lineErrors[] = "Cellule {$colName}{$rowNumber} : la valeur doit être un nombre >= 0.001";
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

                $insert[] = [
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
                    'unitprice'        => $colJ,
                    'from_state' => from_state(),
                ];
            }

            if (count($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => implode('<br/>', $errors),
                ], 422);
            }

            if (!count($insert)) {
                return response()->json([
                    'success' => false,
                    'message' => "Aucune ligne n'a été importée. Veuillez remplir le fichier excel en commençant par la cellule A2.",
                ], 422);
            }

            foreach ($insert as $data) {
                Delivery::firstOrCreate(
                    [
                        'delivery_note' => $colF,
                        'entity_id' => $entity->id
                    ],
                    $data
                );
            }


            return response()->json([
                'success' => true,
                'message' => "Votre fichier a été importé avec succès.",
            ], 201);
        } else {
            if ($user->user_role == 'petrolier') {
                $entity = $user->entities()->first();
            } else if ($user->user_role == 'etatique') {
                $entity  = Entity::findOrFail(request('entity_id'));
            } else {
                abort(403);
            }
            abort_if(!$entity, 422, "No entity");

            $validated = $request->validate([
                'date' => 'required|string|date|before_or_equal:today',
                'terminal'  => 'required|string|max:255',
                'product' => 'required|string|in:' . implode(',', mainfuels()),
                'way' => 'required|string|in:' . implode(',', mainWays()),
                'client'  => 'required|string|max:128',
                'locality'  => 'required|string',
                'delivery_note'  => 'required|string',
                'delivery_program'  => 'required|string',
                'lata'  => 'required|numeric|min:0.001',
                'unitprice'  => 'required|numeric|min:0.001',
                'deliveryfile' => 'nullable|array',
                'deliveryfile.*' => 'mimes:pdf|max:10240'
            ]);

            abort_if('JET' == request('product') && 'NORD' == request('way'), 422, "Le JET n'est vendu qu'au SUD, EST et OUEST");

            DB::beginTransaction();
            $validated['entity_id'] = $entity->id;
            $validated['from_state'] = from_state();

            $delivery = Delivery::create($validated);

            $f = [];
            if ($request->hasFile('deliveryfile')) {
                foreach ($request->file('deliveryfile') as $file) {
                    $f[] = ['delivery_id' => $delivery->id, 'file' =>  $file->store('bills', 'public')];
                }
            }

            foreach ($f as $fi) {
                Deliveryfile::create($fi);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Votre livraison excédentaire a été enregistrée avec succès !",
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'etatique']), 403, "No permission");

        if ($user->user_role == 'etatique') {
            //
        } else {
            $entity = $user->entities()->first();
            abort_if($entity->id != $delivery->entity_id, 403, "Not permit");
        }

        if ('bulk' == request('action')) {
            $ids = (array) json_decode(request('ids'));
            $deliveries = Delivery::whereIn('id', $ids)->get();
            DB::beginTransaction();
            foreach ($deliveries as $delivery) {
                foreach ($delivery->deliveryfiles as $f) {
                    File::delete("storage/" . $f->file);
                }
                $delivery->delete();
            }
            DB::commit();
            $n = count($ids);

            return response()->json([
                'success' => true,
                'message' => "Vous avez supprimé $n livraison(s) avec succès !",
            ], 200);
        }

        foreach ($delivery->deliveryfiles as $f) {
            File::delete("storage/" . $f->file);
        }

        $delivery->delete();

        return response()->json([
            'success' => true,
            'message' => "Vous avez supprimé la livraison #$delivery->id avec succès !",
        ], 200);
    }
}
