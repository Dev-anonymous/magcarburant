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
            ->addColumn('raw_data', function ($row) use ($user) {
                return json_encode([
                    'id' => $row->id,
                    'name' => $row->name,
                    'perms' => $row->permissions->pluck('id')->all(),
                ]);
            })
            ->rawColumns(['raw_data'])
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
