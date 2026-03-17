<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Recovery;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WebController extends Controller
{
    function applogs()
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'etatique', 'logisticien', 'sudo']), 403, "No permission");
        return view('applogs');
    }

    function roles()
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'etatique', 'logisticien']), 403, "No permission");
        $permissions = Permission::where('user_role', $user->user_role)->orderBy('name')->get();
        return view('roles', compact('permissions'));
    }

    function users()
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'etatique', 'logisticien']), 403, "No permission");
        $roles = $user->roles;
        return view('users', compact('roles'));
    }

    function recovery()
    {
        return view('recovery');
    }

    function recovery_verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ], ['email.exists' => "Aucun compte trouvé avec cette adresse email."]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();
        if (!$user->email) {
            return response()->json(['success' => false, 'message' => "Nous ne pouvons pas réinitialiser votre mot de passe pour le moment."]);
        }

        $recovery = Recovery::where('email', $email)
            ->where('used', false)
            ->where('date', '>=', Carbon::now()->subMinutes(15))
            ->first();

        if (!$recovery) {
            $recovery = Recovery::create([
                'email' => $email,
                'token' => Str::random(64),
                'date' => Carbon::now(),
                'used' => false,
            ]);
        }

        // Envoi du mail avec le token
        Mail::send('emails.recovery', ['token' => $recovery->token], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Réinitialisation de votre mot de passe');
        });

        return response()->json([
            'success' => true,
            'message' => "Un email de réinitialisation a été envoyé."
        ]);
    }
}
