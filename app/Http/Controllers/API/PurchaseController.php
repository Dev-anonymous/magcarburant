<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
        $user = auth()->user();
        abort_if(!in_array($user->user_role, ['provider']), 403, "No permission");

        if ($user->user_role == 'provider') {
            $entity = $user->entities()->first();
        } else {
            abort(403);
            // $entity = Entity::find(request('entity_id'));
        }
        abort_if(!$entity, 422, "No entity");
        $purchases = $entity->purchases();

        $date = request('date');
        $date = explode(' to ', $date);
        $date = array_filter($date);
        $from = @$date[0] ?? nnow()->toDateString();
        $to = @$date[1] ?? $from;

        $purchases->whereBetween('date', [$from, $to]);

        return DataTables::of($purchases)
            ->addIndexColumn()
            ->editColumn('date', function ($row) {
                return $row->date?->format('d-m-Y');
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
            ->rawColumns(['action', 'purchasefile'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_if(!in_array($user->user_role, ['provider']), 403, "No permission");

        if (request('action') == 'update') {
            $id = request('id');
            $purchase = Purchase::findOrFail($id);

            $validated = $request->validate([
                'date' => 'required|string|date|before_or_equal:today',
                'product' => 'required|string|in:' . implode(',', mainfuels()),
                'provider'  => 'required|string|max:128',
                'billnumber'  => 'required|string|unique:purchase,billnumber,' . $purchase->id,
                'unitprice'  => 'required|numeric|min:1',
                'qtytm'  => 'required|numeric|min:1',
                'qtym3'  => 'required|numeric|min:1',
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
                Purchasefile::insert($newFiles);
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

            if ($user->user_role == 'provider') {
                $entity = $user->entities()->first();
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
                $colD = trim($row[3] ?? null);
                $colE = trim($row[4] ?? null);
                $colF = trim($row[5] ?? null);
                $colG = trim($row[6] ?? null);
                $colH = trim($row[7] ?? null);

                $row = array_splice($row, 0, 8);

                if (empty(array_filter($row))) {
                    continue;
                }
                $lineErrors = [];

                if (empty($colA)) {
                    $lineErrors[] = "Cellule A$rowNumber : veuillez renseigner la date de l'achat";
                }
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
                if (empty($colB)) {
                    $lineErrors[] = "Cellule B$rowNumber : veuillez renseigner le nom du produit";
                } else {
                    if (!in_array($colB,  mainfuels(), true)) {
                        $lineErrors[] = "Cellule D$rowNumber : le produit \"$colB\" n'est pas reconnu";
                    }
                }
                if (empty($colC)) {
                    $lineErrors[] = "Cellule C$rowNumber : veuillez renseigner le nom du fournisseur";
                }
                if (empty($colD)) {
                    $lineErrors[] = "Cellule D$rowNumber : veuillez renseigner le numéro facture";
                } else {
                    $exi = Purchase::where(['entity_id' => $entity->id, 'billnumber' => $colD])->exists();
                    if ($exi) {
                        $lineErrors[] = "Cellule D$rowNumber : l'achat avec ce numéro facture $colD est déjà enregistré";
                    }
                }
                foreach (['E' => $colE, 'F' => $colF, 'G' => $colG, 'H' => $colH] as $colName => $value) {
                    if (!is_numeric($value) || $value < 0) {
                        $lineErrors[] = "Cellule $colName$rowNumber : la valeur doit être un nombre >= 0";
                    }
                }
                if (!empty($lineErrors)) {
                    $errors[] =  implode("; ", $lineErrors);
                    continue;
                }

                $insert[] = [
                    'entity_id' => $entity->id,
                    'date' => $colA,
                    'product' => $colB,
                    'provider' => $colC,
                    'billnumber' => $colD,
                    'unitprice' => $colE,
                    'qtytm' => $colF,
                    'qtym3' => $colG,
                    'density' => $colH,
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
                    'message' => "Aucune ligne n'a été importé. Veuillez remplir le fichier excel en commençant par la cellule A2.",
                ], 422);
            }

            Purchase::insertOrIgnore($insert);

            return response()->json([
                'success' => true,
                'message' => "Votre fichier a été importé avec succès.",
            ], 201);
        } else {
            if ($user->user_role == 'provider') {
                $entity = $user->entities()->first();
            } else {
                abort(403);
            }
            abort_if(!$entity, 422, "No entity");

            $validated = $request->validate([
                'date' => 'required|string|date|before_or_equal:today',
                'product' => 'required|string|in:' . implode(',', mainfuels()),
                'provider'  => 'required|string|max:128',
                'billnumber'  => 'required|string|unique:purchase',
                'unitprice'  => 'required|numeric|min:1',
                'qtytm'  => 'required|numeric|min:1',
                'qtym3'  => 'required|numeric|min:1',
                'density'  => 'required|numeric',
                'purchasefile' => 'nullable|array',
                'purchasefile.*' => 'mimes:pdf|max:10240'
            ], ['billnumber.unique' => "Le numéro de la facture existe déjà."]);

            DB::beginTransaction();
            $validated['entity_id'] = $entity->id;
            $sale = Purchase::create($validated);

            $f = [];
            if ($request->hasFile('purchasefile')) {
                foreach ($request->file('purchasefile') as $file) {
                    $f[] = ['purchase_id' => $sale->id, 'file' =>  $file->store('bills', 'public')];
                }
            }
            Purchasefile::insert($f);

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
        $user = auth()->user();
        abort_if(!in_array($user->user_role, ['provider']), 403, "No permission");
        $entity = $user->entities()->first();
        abort_if($entity->id != $purchase->entity_id, 403, "Not permit");

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
