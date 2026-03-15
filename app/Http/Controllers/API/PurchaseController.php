<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Purchase;
use App\Models\Purchasefile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
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

        $date = request('date');
        $date = explode(' to ', $date);
        $date = array_filter($date);
        $from = @$date[0] ?? nnow()->toDateString();
        $to = @$date[1] ?? $from;

        $purchases = $entity->purchases()->whereBetween('date', [$from, $to]);
        if (from_state()) {
            $purchases->where('from_state', 1);
        } else {
            $purchases->where('from_state', 0);
        }

        return DataTables::of($purchases)
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
            })->editColumn('unitprice', function ($row) {
                return v($row->unitprice);
            })->editColumn('qtytm', function ($row) {
                return v($row->qtytm);
            })->editColumn('qtym3', function ($row) {
                return v($row->qtym3);
            })->editColumn('density', function ($row) {
                return v($row->density);
            })->editColumn('purchasefile', function ($row) {
                $f = '';
                foreach ($row->purchasefiles as $i => $e) {
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
            ->rawColumns(['action', 'purchasefile', 'selall'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'etatique']), 403, "No permission");

        if (request('action') == 'update') {
            $id = request('id');
            $purchase = Purchase::findOrFail($id);

            $validated = $request->validate([
                'date' => 'required|string|date|before_or_equal:today',
                'product' => 'required|string|in:' . implode(',', mainfuels()),
                'way' => 'required|string|in:' . implode(',', mainWays()),
                'provider'  => 'required|string|max:128',
                'billnumber'  => 'required|string|unique:purchase,billnumber,' . $purchase->id,
                'unitprice'  => 'required|numeric|min:0.001',
                'qtytm'  => 'required|numeric|min:0.001',
                'qtym3'  => 'required|numeric|min:0.001',
                'density'  => 'required|numeric',
                'purchasefile' => 'nullable|array',
                'purchasefile.*' => 'mimes:pdf|max:10240'
            ], ['billnumber.unique' => "Le numéro de la facture existe déjà."]);
            DB::beginTransaction();

            $purchase->update($validated);
            $newFiles = [];
            if ($request->hasFile('purchasefile')) {
                foreach ($request->file('purchasefile') as $file) {
                    $newFiles[] = [
                        'purchase_id' => $purchase->id,
                        'file' => $file->store('bills', 'public')
                    ];
                }
            }

            if (!empty($newFiles)) {
                foreach ($purchase->purchasefiles as $f) {
                    File::delete("storage/" . $f->file);
                    $f->delete();
                }
                foreach ($newFiles as $i) {
                    Purchasefile::create($i);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "L'achat a été mise à jour avec succès !",
            ]);
        } elseif (request('action') == 'import') {
            $validated = $request->validate([
                'file' => 'required|file|mimes:xlsx,xls'
            ]);

            if ($user->user_role == 'petrolier') {
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
                $colA = trim($row[0] ?? null); // date
                $colB = trim($row[1] ?? null); // zone
                $colC = trim($row[2] ?? null); // product
                $colD = trim($row[3] ?? null); // provider
                $colE = trim($row[4] ?? null); // billnumber
                $colF = trim($row[5] ?? null); // unitprice
                $colG = trim($row[6] ?? null); // qtytm
                $colH = trim($row[7] ?? null); // qtym3
                $colI = trim($row[8] ?? null); // density

                if (empty(array_filter([$colA, $colB, $colC, $colD, $colE, $colF, $colG, $colH, $colI]))) {
                    continue;
                }

                $lineErrors = [];

                // Date
                if (empty($colA)) {
                    $lineErrors[] = "Cellule A$rowNumber : veuillez renseigner la date de l'achat";
                } else {
                    try {
                        if (is_numeric($colA)) {
                            $colA = Date::excelToDateTimeObject($colA)->format('Y-m-d');
                        } else {
                            $colA = Carbon::parse(str_replace('/', '-', $colA))->format('Y-m-d');
                        }
                        $date = Carbon::parse($colA);
                        if ($date->gt(Carbon::today())) {
                            $lineErrors[] = "Cellule A$rowNumber : la date d'achat {$date->format('d-m-Y')} ne doit pas être > aujourd'hui";
                        }
                    } catch (\Throwable $th) {
                        $lineErrors[] = "Cellule A$rowNumber : la date est invalide ou au mauvais format";
                    }
                }

                // Zone
                if (empty($colB)) {
                    $lineErrors[] = "Cellule B$rowNumber : veuillez renseigner la zone";
                } else {
                    if (!in_array(strtoupper($colB), mainWays())) {
                        $lineErrors[] = "Cellule B$rowNumber : La zone \"$colB\" n'est pas valide";
                    }
                }

                // Product
                if (empty($colC)) {
                    $lineErrors[] = "Cellule C$rowNumber : veuillez renseigner le nom du produit";
                } else {
                    if (!in_array(strtoupper($colC), mainfuels(), true)) {
                        $lineErrors[] = "Cellule C$rowNumber : le produit \"$colC\" n'est pas reconnu";
                    }
                }

                // Provider
                if (empty($colD)) {
                    $lineErrors[] = "Cellule D$rowNumber : veuillez renseigner le nom du fournisseur";
                }

                // Billnumber
                if (empty($colE)) {
                    $lineErrors[] = "Cellule E$rowNumber : veuillez renseigner le numéro facture";
                } else {
                    $exi = Purchase::where(['entity_id' => $entity->id, 'billnumber' => $colE, 'from_state' => 0])->exists();
                    if ($exi && $user->user_role === 'petrolier') {
                        $lineErrors[] = "Cellule E$rowNumber : l'achat avec le numéro facture $colE est déjà enregistré";
                    }
                    $exi = Purchase::where(['entity_id' => $entity->id, 'billnumber' => $colE, 'from_state' => 1])->exists();
                    if ($exi && $user->user_role === 'etatique') {
                        $lineErrors[] = "Cellule E$rowNumber : l'achat avec le numéro facture $colE est déjà enregistré";
                    }
                }

                // Numeric checks (unitprice, qtytm, qtym3, density)
                foreach (['F' => $colF, 'G' => $colG, 'H' => $colH, 'I' => $colI] as $colName => $value) {
                    if (!is_numeric($value) || $value < 0) {
                        $lineErrors[] = "Cellule $colName$rowNumber : la valeur doit être un nombre >= 0";
                    }
                }

                if (!empty($lineErrors)) {
                    $errors[] = implode("; ", $lineErrors);
                    continue;
                }

                $insert[] = [
                    'entity_id' => $entity->id,
                    'date' => $colA,
                    'way' => strtoupper($colB),
                    'product' => strtoupper($colC),
                    'provider' => $colD,
                    'billnumber' => $colE,
                    'unitprice' => $colF,
                    'qtytm' => $colG,
                    'qtym3' => $colH,
                    'density' => $colI,
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

            foreach ($insert as $data) Purchase::create($data);

            return response()->json([
                'success' => true,
                'message' => "Votre fichier a été importé avec succès.",
            ], 201);
        } else {
            if ($user->user_role == 'petrolier') {
                $entity = $user->entities()->first();
            } elseif ($user->user_role == 'etatique') {
                $entity  = Entity::findOrFail(request('entity_id'));
            } else {
                abort(403);
            }
            abort_if(!$entity, 422, "No entity");

            $validated = $request->validate([
                'date' => 'required|string|date|before_or_equal:today',
                'product' => 'required|string|in:' . implode(',', mainfuels()),
                'way' => 'required|string|in:' . implode(',', mainWays()),
                'provider'  => 'required|string|max:128',
                'billnumber'  => 'required|string|unique:purchase',
                'unitprice'  => 'required|numeric|min:0.001',
                'qtytm'  => 'required|numeric|min:0.001',
                'qtym3'  => 'required|numeric|min:0.001',
                'density'  => 'required|numeric',
                'purchasefile' => 'nullable|array',
                'purchasefile.*' => 'mimes:pdf|max:10240'
            ], ['billnumber.unique' => "Le numéro de la facture existe déjà."]);

            DB::beginTransaction();
            $validated['entity_id'] = $entity->id;
            $validated['from_state'] = from_state();
            $sale = Purchase::create($validated);

            $f = [];
            if ($request->hasFile('purchasefile')) {
                foreach ($request->file('purchasefile') as $file) {
                    $f[] = ['purchase_id' => $sale->id, 'file' =>  $file->store('bills', 'public')];
                }
            }
            foreach ($f as $i) {
                Purchasefile::create($i);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Votre achat a été enregistré avec succès !",
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'etatique']), 403, "No permission");
        if ($user->user_role == 'etatique') {
            //
        } else {
            $entity = $user->entities()->first();
            abort_if($entity->id != $purchase->entity_id, 403, "Not permit");
        }


        if ('bulk' == request('action')) {
            $ids = (array) json_decode(request('ids'));
            $purchases = Purchase::whereIn('id', $ids)->get();
            DB::beginTransaction();
            foreach ($purchases as $purchase) {
                foreach ($purchase->purchasefiles as $f) {
                    File::delete("storage/" . $f->file);
                }
                $purchase->delete();
            }
            DB::commit();
            $n = count($ids);

            return response()->json([
                'success' => true,
                'message' => "Vous avez supprimé $n achats(s) avec succès !",
            ], 200);
        }


        foreach ($purchase->purchasefiles as $f) {
            File::delete("storage/" . $f->file);
        }

        $purchase->delete();

        return response()->json([
            'success' => true,
            'message' => "Vous avez supprimé l'achat $purchase->billnumber avec succès !",
        ], 200);
    }
}
