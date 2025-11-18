<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Purchasefile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $purchases = $entity->purchases;

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
                return "<div class='d-flex'>$f</div>";
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
            ]);
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
            ]);

            DB::beginTransaction();
            $validated['entity_id'] = $entity->id;
            $sale = Purchase::create($validated);

            $f = [];
            if ($request->hasFile('purchasefile')) {
                foreach ($request->file('purchasefile') as $file) {
                    $f[] = ['sale_id' => $sale->id, 'file' =>  $file->store('bills', 'public')];
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
        //
    }
}
