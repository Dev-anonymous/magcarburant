<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Salefile;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $user = auth()->user();
        abort_if(!in_array($user->user_role, ['provider']), 403, "No permission");

        if ($user->user_role == 'provider') {
            $entity = $user->entities()->first();
        } else {
            abort(403);
            // $entity = Entity::find(request('entity_id'));
        }
        abort_if(!$entity, 422, "No entity");
        $sales = $entity->sales();

        $date = request('date');
        $date = explode(' to ', $date);
        $date = array_filter($date);
        $from = @$date[0] ?? nnow()->toDateString();
        $to = @$date[1] ?? $from;

        $sales->whereBetween('date', [$from, $to]);

        return DataTables::of($sales)
            ->addIndexColumn()
            ->editColumn('date', function ($row) {
                return $row->date?->format('d-m-Y');
            })->editColumn('salefile', function ($row) {
                $f = '';
                foreach ($row->salefiles as $i => $e) {
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
            ->rawColumns(['action', 'salefile'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (request('action') == 'update') {
            $id = request('id');
            $sale = Sale::findOrFail($id);

            $validated = $request->validate([
                'date' => 'required|string|date|before_or_equal:today',
                'product' => 'required|string|in:' . implode(',', mainfuels()),
                'way' => 'required|string|in:' . implode(',', mainWays()),
                'client'  => 'required|string|max:128',
                'locality'  => 'required|string',
                'delivery_note'  => 'required|string',
                'delivery_program'  => 'required|string',
                'lata'  => 'required|numeric|min:0',
                'l15'  => 'required|numeric|min:0',
                'density'  => 'required|numeric|min:1',
                'salefile' => 'nullable|array',
                'salefile.*' => 'mimes:pdf|max:10240'
            ]);

            DB::beginTransaction();

            $sale->update($validated);

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
                    Salefile::insert($insertFiles);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Votre vente a été mise à jour avec succès !",
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
                $colI = trim($row[8] ?? null);
                $colJ = trim($row[9] ?? null);
                $row = array_splice($row, 0, 10);


                if (empty(array_filter($row))) {
                    continue;
                }
                $lineErrors = [];

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
                        $lineErrors[] = "Cellule A$rowNumber : la date de la vente {$date->format('d-m-Y')} ne doit pas être > aujourd'hui";
                    }
                } catch (\Throwable $th) {
                    $lineErrors[] = "Cellule A$rowNumber : la date est invalide ou au mauvais format";
                }
                if (empty($colB)) {
                    $lineErrors[] = "Cellule B$rowNumber : veuillez renseigner la localité";
                }
                if (!in_array($colC, mainWays(), true)) {
                    $lineErrors[] = "Cellule C$rowNumber : la voie \"$colC\" n'est pas valide";
                }

                if (empty($colD)) {
                    $lineErrors[] = "Cellule D$rowNumber : veuillez renseigner le nom du produit";
                } else {
                    if (!in_array($colD,  mainfuels(), true)) {
                        $lineErrors[] = "Cellule D$rowNumber : le produit \"$colD\" n'est pas reconnu";
                    }
                }

                if (empty($colE)) {
                    $lineErrors[] = "Cellule E$rowNumber : veuillez renseigner le bon de livraison";
                }
                if (empty($colF)) {
                    $lineErrors[] = "Cellule F$rowNumber : veuillez renseigner le programme de livraison";
                }
                if (empty($colG)) {
                    $lineErrors[] = "Cellule G$rowNumber : veuillez renseigner le nom du client";
                }
                foreach (['H' => $colH, 'I' => $colI, 'J' => $colJ] as $colName => $value) {
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
                    'locality' => $colB,
                    'way' => $colC,
                    'product' => $colD,
                    'delivery_note' => $colE,
                    'delivery_program' => $colF,
                    'client' => $colG,
                    'lata' => $colH,
                    'l15' => $colI,
                    'density' => $colJ,
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

            Sale::insertOrIgnore($insert);

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
                'way' => 'required|string|in:' . implode(',', mainWays()),
                'client'  => 'required|string|max:128',
                'locality'  => 'required|string',
                'delivery_note'  => 'required|string',
                'delivery_program'  => 'required|string',
                'lata'  => 'required|numeric|min:0',
                'l15'  => 'required|numeric|min:0',
                'density'  => 'required|numeric|min:1',
                'salefile' => 'nullable|array',
                'salefile.*' => 'mimes:pdf|max:10240'
            ]);

            DB::beginTransaction();
            $validated['entity_id'] = $entity->id;

            $sale = Sale::create($validated);

            $f = [];
            if ($request->hasFile('salefile')) {
                foreach ($request->file('salefile') as $file) {
                    $f[] = ['sale_id' => $sale->id, 'file' =>  $file->store('bills', 'public')];
                }
            }
            Salefile::insert($f);

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
        $user = auth()->user();
        abort_if(!in_array($user->user_role, ['provider']), 403, "No permission");
        $entity = $user->entities()->first();
        abort_if($entity->id != $sale->entity_id, 403, "Not permit");

        foreach ($sale->salefiles as $f) {
            File::delete("storage/" . $f->file);
        }

        $sale->delete();

        return response()->json([
            'success' => true,
            'message' => "Vous avez supprimé la vente #$sale->id avec succès !",
        ], 200);
    }
}
