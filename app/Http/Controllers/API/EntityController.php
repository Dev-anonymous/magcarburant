<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Entity::whereHas('user', fn($q) => $q->where('user_role', 'provider'))->orderBy('shortname')->with('user')->get()->map(function ($el) {
            $el->logo = $el->logo ? asset('storage/' . $el->logo) : asset('assets/images/entity.png');
            return $el;
        });
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (request('action') == 'update') {
            $id = request('id');
            $entity = Entity::findOrFail($id);
            $user = $entity->user;

            $validated = $request->validate([
                'shortname' => 'required|string|max:60|unique:entity,shortname,' . $entity->id,
                'email' => 'required|string|max:60|unique:users,email,' . $user->id,
                'longname'  => 'required|string|max:128',
                'logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('logo')) {
                $validated['logo'] = $request->file('logo')->store('logo', 'public');
                try {
                    Storage::disk('public')->delete($entity->logo);
                } catch (\Throwable $th) {
                }
            }

            $validated['shortname'] = ucfirst($validated['shortname']);

            DB::beginTransaction();

            try {
                $user->name = $validated['shortname'];
                $user->email = $validated['email'];
                $user->save();

                $entity->shortname = $validated['shortname'];
                $entity->longname  = $validated['longname'];
                if (isset($validated['logo'])) {
                    $entity->logo = $validated['logo'];
                }
                $entity->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Le fournisseur {$entity->shortname} a été mise à jour avec succès !",
                    'data'    => $entity
                ], 200);
            } catch (\Throwable $e) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage(),
                ], 500);
            }
        } else {
            $validated = $request->validate([
                'shortname' => 'required|string|max:60|unique:entity',
                'email' => 'required|string|max:60|unique:users',
                'longname'  => 'required|string|max:128',
                'password'  => 'nullable|string|max:128',
                'logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('logo')) {
                $validated['logo'] = $request->file('logo')->store('logo', 'public');
            }

            $validated['shortname'] = ucfirst($validated['shortname']);

            DB::beginTransaction();
            $user = new User();
            $user->user_role = 'provider';
            $user->name = $validated['shortname'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->save();

            $entity = new Entity;
            $entity->users_id = $user->id;
            $entity->shortname = $validated['shortname'];
            $entity->longname = $validated['longname'];
            $entity->logo = @$validated['logo'];
            $entity->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Vous avez créé le fournisseur $user->name avec succès !",
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Entity $entity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entity $entity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entity $entity)
    {
        $user = $entity->user;

        DB::beginTransaction();

        try {
            Storage::disk('public')->delete($entity->logo);
        } catch (\Throwable $th) {
        }
        $user->delete();
        $entity->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "Vous avez supprimé le fournisseur $user->name avec succès !",
        ], 200);
    }
}
