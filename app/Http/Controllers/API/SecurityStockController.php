<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\SecurityStock;
use App\Models\Securitystockfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\DataTables;

class SecurityStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $isState = false;
        if (isPetroUser() || isLogUser()) {
            can('Stock de sécurité collecté reversé - Lire', true);
            $entity = gentity();
        } elseif (isEtaUser()) {
            can('Mode écriture - Lire', true);
            $entity  = Entity::findOrFail(request('entity_id'));
            $isState = true;
        } else {
            abort(403);
        }
        abort_if(!$entity, 422, "No entity");
        $data = $entity->security_stocks()->where('from_state', from_state());


        $year = request('year', nnow()->year);
        $data->whereYear('month', $year);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('month', function ($row) {
                $date = $row->month;
                $m = ucfirst($date->translatedFormat('F')) . ' ' . $date->format('Y');
                return $m;
            })
            ->addColumn('amount', function ($row) {
                return v($row->amount);
            })
            ->addColumn('files', function ($row) {
                $f = '';
                foreach ($row->securitystockfiles as $i => $e) {
                    $u = asset('storage/' . $e->file);
                    $f .= "<a href='$u' class='text-nowrap mr-2' target='_blank'><i class='material-icons md-18 align-middle mb-1 text-primary'>attach_file</i> Fichier " . ($i + 1) . "</a>";
                }
                return "<div class=''>$f</div>";
            })
            ->addColumn('action', function ($row) use ($isState) {
                $t = '';
                $can = $isState ? can('Mode écriture - Modifier') : can('Stock de sécurité collecté reversé - Modifier');
                if ($can) {
                    $date = $row->month;
                    $m = ucfirst($date->translatedFormat('F')) . ' ' . $date->format('Y');
                    $data = e(json_encode(array_merge($row->toArray(), ['monthname' => $m])));
                    $t = "<button class='btn btn-sm btn-primary editdata'  data-data='$data'>
                    <i class='material-icons md-14 align-middle'>edit</i>
                    <span class='align-middle'>Modifier</span>
                    </button>";
                }

                if (isEtaUser()) {
                    if (from_state()) {
                        return $t;
                    }
                } else {
                    return $t;
                }
            })
            ->rawColumns(['action', 'files'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (request('action') == 'update') {
            abort_if(!isProLogEtaUser(), 403, "No permission");
            $isState = isEtaUser();

            if ($isState) {
                can('Mode écriture - Modifier', true);
            } else {
                can('Stock de sécurité collecté reversé - Modifier', true);
            }

            $validated = $request->validate([
                'id' => 'required|exists:security_stock',
                'amount' => 'required|numeric|min:0',
                'securitystockfile' => 'nullable|array',
                'securitystockfile.*' => 'mimes:pdf|max:10240'
            ]);

            $securitystock = SecurityStock::findOrFail(request('id'));

            DB::beginTransaction();
            if ($request->hasFile('securitystockfile')) {
                $insertFiles = [];
                foreach ($request->file('securitystockfile') as $file) {
                    $insertFiles[] = [
                        'security_stock_id' => $securitystock->id,
                        'file' => $file->store('bills', 'public')
                    ];
                }
                if (!empty($insertFiles)) {
                    foreach ($securitystock->securitystockfiles as $f) {
                        File::delete("storage/" . $f->file);
                        $f->delete();
                    }
                    foreach ($insertFiles as $f) {
                        Securitystockfile::create($f);
                    }
                }
            }

            $securitystock->amount = request('amount');
            $securitystock->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Le montant a été mis à jour avec succès !",
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SecurityStock $securitystock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SecurityStock $securitystock) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SecurityStock $securitystock)
    {
        //
    }
}
