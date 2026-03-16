<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'logisticien', 'etatique']), 403, "No permission");

        $data = User::whereIn('id', childrenlist($user, false));

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return $row->created_at?->format('d-m-Y H:i:s');
            })
            ->editColumn('role_id', function ($row) {
                return $row->role->name;
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
                return $t;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'logisticien', 'etatique']), 403, "No permission");

        if (request('action') == 'update') {
            $u = User::findOrFail(request('id'));
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    Rule::unique('users')->ignore($u->id)->where(function ($query) use ($user) {
                        return $query->where('user_id', $user->user_id);
                    }),
                ],
                'email' => [
                    'required',
                    'string',
                    'max:60',
                    Rule::unique('users')->ignore($u->id),
                ],
                'role_id' => 'required|in:' . implode(',', $user->roles()->pluck('id')->all()),
            ]);

            DB::beginTransaction();
            $u->role_id = $validated['role_id'];
            $u->name = ucfirst($validated['name']);
            $u->email = strtolower($validated['email']);
            $u->save();
            $u->tokens()->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "L'utilisateur {$u->name} a été mis à jour avec succès !",
            ], 200);
        } else {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    Rule::unique('users')->where(function ($query) use ($user) {
                        return $query->where('user_id', $user->id);
                    }),
                ],
                'email' => 'required|string|max:60|unique:users',
                'role_id'  => 'required|in:' . implode(',',  $user->roles()->pluck('id')->all()),
                'password'  => 'nullable|string|max:128',
            ]);

            DB::beginTransaction();
            $nuser = new User();
            $nuser->user_role = 'utilisateur';
            $nuser->user_id = $user->id;
            $nuser->role_id = $validated['role_id'];
            $nuser->name = ucfirst($validated['name']);
            $nuser->email = strtolower($validated['email']);
            $nuser->password = Hash::make(request('password', 'mdp@123'));
            $nuser->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Vous avez créé l'utilisateur $nuser->name avec succès !",
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $u = request()->user();
        abort_if(!in_array($u->user_role, ['petrolier', 'logisticien', 'etatique']), 403, "No permission");
        abort_if($user->user_id !== $u->id, 403, "Not permit");
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => "Vous avez supprimé l'utilisateur $user->name avec succès !",
        ], 200);
    }
}
