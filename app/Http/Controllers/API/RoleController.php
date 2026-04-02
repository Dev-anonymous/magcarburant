<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleHasPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        can('Gestion des rôles - Lire', true);

        $user = request()->user();
        abort_if(!isProLogEtaUser(), 403, "No permission");

        $parent = $user->user ?? $user;
        $roles = $parent->roles;

        return DataTables::of($roles)
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return ucfirst($row->name);
            })
            ->addColumn('module', function ($row) {
                $perm = $row->permissions->pluck('name')->all();
                $modules = array_values(array_unique(array_map(function ($item) {
                    return explode(' - ', $item)[0];
                }, $perm)));
                $n = count($modules);
                if ($n) {
                    $l = "($n) " . implode(', ', $modules);
                    return Str::limit($l, 100,);
                }
                return '-';
            })

            ->addColumn('action', function ($row) {
                $data = e(json_encode([
                    'id' => $row->id,
                    'name' => $row->name,
                    'perms' => $row->permissions->pluck('id')->all(),
                ]));

                $btn = "";
                $btn1 = "
                    <a class='dropdown-item' href='#' bedit data='$data'>
                        <i class='material-icons md-14 align-middle'>edit</i>
                        <span class='align-middle'>Modifier</span>
                    </a>
                ";
                $btn2 = "
                        <a class='dropdown-item text-danger' href='#' bdel data='$data'>
                            <i class='material-icons md-14 align-middle'>delete</i>
                            <span class='align-middle'>Supprimer</span>
                        </a>";

                if (can('Gestion des rôles - Modifier')) {
                    $btn .= $btn1;
                }
                if (can('Gestion des rôles - Supprimer')) {
                    $btn .= $btn2;
                }

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
                            $btn
                        </div>
                    </div>
                DATA;

                if (empty($btn)) {
                    $t = '';
                }
                return $t;
            })
            ->rawColumns(['raw_data', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = request()->user();
        abort_if(!isProLogEtaUser(), 403, "No permission");

        if (request('action') == 'update') {
            can('Gestion des rôles - Modifier', true);

            $role = Role::findOrFail(request('id'));

            $parent = $user->user ?? $user;

            $request->validate([
                'name' => [
                    'required',
                    'string',
                    Rule::unique('roles')->where(function ($query) use ($parent) {
                        return $query->where('users_id', $parent->id);
                    })->ignore($role->id),
                ],
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            DB::transaction(function () use ($request, $parent, $role) {
                $role->update([
                    'name' => strtolower($request->name),
                ]);

                RoleHasPermission::where('role_id', $role->id)->delete();
                if ($request->filled('permissions')) {
                    $permissions = Permission::whereIn('name', $request->permissions)
                        ->where('user_role', $parent->user_role)
                        ->get();

                    foreach ($permissions as $permission) {
                        RoleHasPermission::create([
                            'role_id' => $role->id,
                            'permission_id' => $permission->id,
                        ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Rôle mis à jour avec succès',
            ]);
        } else {
            can('Gestion des rôles - Créer', true);
            $parent = $user->user ?? $user;

            $request->validate([
                'name' => [
                    'required',
                    'string',
                    Rule::unique('roles')->where(function ($query) use ($parent) {
                        return $query->where('users_id', $parent->id);
                    }),
                ],
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            DB::transaction(function () use ($request, $parent) {
                $role = Role::create([
                    'name' => strtolower($request->name),
                    'users_id' => $parent->id,
                ]);
                $permissions = Permission::whereIn('name', (array) $request->permissions)->where('user_role', $parent->user_role)->get();
                foreach ($permissions as $permission) {
                    RoleHasPermission::create([
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Rôle créé avec succès',
            ]);
        }
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
        can('Gestion des rôles - Supprimer', true);

        $user = request()->user();
        abort_if(!isProLogEtaUser(), 403, "No permission");

        $parent = $user->user ?? $user;

        $i = childrenlist($parent, false);
        $n = User::whereIn('id', $i)->where('role_id', $role->id)->count();

        abort_if($n, 422, "Vous ne pouvez pas supprimer ce rôle pour le moment, car il contient $n utilisateur(s)");

        $role->delete();
        return response()->json(['success' => true, 'message' => 'Rôle supprimé']);
    }
}
