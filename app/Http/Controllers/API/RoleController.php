<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'logisticien', 'etatique']), 403, "No permission");

        $roles = $user->roles;

        return DataTables::of($roles)
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return ucfirst($row->name);
            })
            ->addColumn('permission', function ($row) {})
            ->addColumn('module', function ($row) {})
            ->addColumn('action', function ($row) use ($user) {
                // $eb = "";
                // $d = $row->toArray();
                // $d['date'] = $row->date?->format('Y-m-d');
                // $data = e(json_encode($d));
                // $eb = "
                //     <a class='dropdown-item' href='#' bedit data='$data'>
                //         <i class='material-icons md-14 align-middle'>edit</i>
                //         <span class='align-middle'>Modifier</span>
                //     </a>
                // ";
                // $t = <<<DATA
                //     <div class="dropdown">
                //         <a
                //             class="btn btn-primary2 btn-sm"
                //             href="#"
                //             role="button"
                //             data-toggle="dropdown"
                //             aria-haspopup="true"
                //             aria-expanded="false"
                //         >
                //             <i class="material-icons md-18 align-middle"
                //             >more_vert</i
                //             >
                //         </a>
                //         <div class="dropdown-menu dropdown-menu-right">
                //             $eb
                //             <a class="dropdown-item text-danger" href="#" bdel data='$data'>
                //                 <i class="material-icons md-14 align-middle">delete</i>
                //                 <span class="align-middle">Supprimer</span>
                //             </a>
                //         </div>
                //     </div>
                // DATA;

                // if ($row->from_mutuality) {
                //     $t = '';
                // }

                // if (in_array($user->user_role, ['petrolier', 'logisticien', 'etatique'])) {
                //     return $t;
                // }
            })
            ->rawColumns(['action', 'salefile', 'selall'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
    }
}
