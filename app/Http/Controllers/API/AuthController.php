<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        $user = auth()->user();

        $user->update(['last_activity' => nnow()]);

        return response([
            'success' => true,
            'message' => "Connexion réussie.",
            'token' => $user->createToken('token_' . time())->plainTextToken,
        ]);
    }

    public function logout(Request $r)
    {
        if (Auth::check()) {
            Auth::guard('web')->logout();
        }
        if (request()->wantsJson()) {
            return response(['message' => "logged out"]);
        }
        return redirect(route('login'));
    }
}
