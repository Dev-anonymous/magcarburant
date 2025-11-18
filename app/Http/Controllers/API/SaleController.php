<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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
            ->rawColumns(['action', 'salefile'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
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
            'message' => "Vous avez supprimé l'achat $sale->billnumber avec succès !",
        ], 200);
    }
}
