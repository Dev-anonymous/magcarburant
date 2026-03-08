<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $attr = $request->all();
        $validator = Validator::make($attr, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'message' => implode(', ', $validator->errors()->all())
            ], 422);
        }

        $data = $validator->validated();

        if (!Auth::attempt($data, request()->has('remember'))) {
            return response([
                'message' => "Email ou mot de passe incorrect"
            ], 422);
        }

        /** @var \App\Models\User $user **/
        $user = Auth::user();

        $user->update(['last_activity' => nnow()]);

        AuditLog::create([
            'user_id'    => $user->id,
            'entity_id'    => $user->entities()->first()?->id,
            'username'    => $user->name,
            'model_type'    => get_class($user),
            'event'     => 'connexion',
            'ip_address' => request()->ip(),
            'user_agent' => ua(),
            'title' => sprintf("%s s'est connecté (ID:%d)", $user->name ?? 'Système', $user->id)
        ]);

        return response([
            'success' => true,
            'message' => "Connexion réussie.",
            'token' => $user->createToken('token_' . time())->plainTextToken,
        ]);
    }

    public function logout(Request $r)
    {
        if (Auth::check()) {
            $user = request()->user();
            Auth::guard('web')->logout();

            AuditLog::create([
                'user_id'    => $user->id,
                'entity_id'    => $user->entities()->first()?->id,
                'username'    => $user->name,
                'model_type'    => get_class($user),
                'event'     => 'déconnexion',
                'ip_address' => request()->ip(),
                'user_agent' => ua(),
                'title' => sprintf("%s s'est déconnecté (ID:%d)", $user->name ?? 'Système', $user->id)
            ]);
        }


        if (request()->wantsJson()) {
            return response(['message' => "logged out"]);
        }
        return redirect(route('login'));
    }
}
