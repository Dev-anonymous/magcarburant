<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AccountingClosure;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AccountingClosureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['etatique']), 403, "No permission");

        $entity = Entity::findOrFail(request('entity_id'));

        $data = AccountingClosure::orderByDesc('id')->where('entity_id', request('enti'));

        return DataTables::of($data)
            ->addIndexColumn()
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

                if ($row->from_mutuality) {
                    $t = '';
                }

                if (in_array($user->user_role, ['petrolier', 'logisticien', 'etatique'])) {
                    return $t;
                }
            })
            ->rawColumns([])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $user = request()->user();
        abort_if(!in_array($user->user_role, ['etatique']), 403, "No permission");

        $validated = $request->validate([
            'closed_until' => 'required|date|before_or_equal:today',
            'entity_id' => 'required|numeric|exists:entity,id',
        ], [
            'closed_until.required' => 'Veuillez renseigner la date de cloture de la cession.',
        ]);

        DB::beginTransaction();
        $validated['closed_by'] = $user->id;
        AccountingClosure::create($validated);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "Vous avez clôturé la cession  avec succès !",
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AccountingClosure $accountingClosure)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccountingClosure $accountingClosure)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountingClosure $accountingClosure)
    {
        //
    }
}
