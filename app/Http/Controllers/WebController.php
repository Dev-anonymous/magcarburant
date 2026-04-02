<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Recovery;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WebController extends Controller
{
    function applogs()
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['sudo']) && !isProLogEtaUser(), 403, "No permission");
        can('Audit - Lire', true);
        return view('applogs');
    }

    function roles()
    {
        can('Gestion des rôles - Lire', true);

        $user = request()->user();
        abort_if(!isProLogEtaUser(), 403, "No permission");
        $parent = $user->user;
        if ($parent) {
            $user = $parent;
        }
        $permissions = Permission::where('user_role', $user->user_role)->orderBy('name')->get();
        return view('roles', compact('permissions'));
    }

    function users()
    {
        can('Gestion des utilisateurs - Lire', true);

        $user = request()->user();
        abort_if(!isProLogEtaUser(), 403, "No permission");

        $parent = $user->user;
        if ($parent) {
            $user = $parent;
        }

        $roles = $user->roles;
        return view('users', compact('roles'));
    }

    function recovery()
    {
        $token = request('token');
        $canreset = false;
        $message = "";
        if ($token) {
            $recovery = Recovery::where('token', $token)->where('used', false)->where('date', '>=', Carbon::now()->subMinutes(15))->first();
            if ($recovery) {
                $canreset = true;
            } else {
                $message = "Ce lien de réinitialisation est invalide ou a expiré.";
            }
        }
        return view('recovery', compact('canreset', 'message', 'token'));
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

        try {
            Mail::send('emails.recovery', ['token' => $recovery->token, 'user' => $user], function ($message) use ($email) {
                $message->to($email);
                $message->subject('Réinitialisation de votre mot de passe');
            });
            return response()->json([
                'success' => true,
                'message' => "Le lien de réinitialisation a été envoyé à votre email."
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Nous ne pouvons pas réinitialiser votre mot de passe pour le moment, veuillez réessayer."
            ]);
        }
    }

    function recovery_reset(Request $request)
    {
        $request->validate([
            'token' => 'required|exists:recovery,token',
            'password' => 'required|min:6|confirmed',
        ], ['token.exists' => "Lien de réinitialisation invalide."]);

        $token = $request->input('token');
        $recovery = Recovery::where('token', $token)
            ->where('used', false)
            ->where('date', '>=', Carbon::now()->subMinutes(15))
            ->first();

        if (!$recovery) {
            return response()->json(['success' => false, 'message' => "Lien de réinitialisation invalide ou expiré."]);
        }

        $user = User::where('email', $recovery->email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => "Aucun compte trouvé pour ce lien de réinitialisation."]);
        }

        try {
            DB::beginTransaction();

            Mail::send('emails.template', [
                'subject' => 'Mot de passe réinitialisé',
                'content' => '<h2>Bonjour ' . $user->name . ',</h2>
                          <p>Votre mot de passe a été réinitialisé avec succès le ' . nnow()->format('d-m-Y H:i') . '.</p>
                          <p>Si vous n’êtes pas à l’origine de cette action, contactez immédiatement le support.</p>'
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Mot de passe réinitialisé');
            });

            $user->password = Hash::make($request->input('password'));
            $user->save();

            $recovery->used = true;
            $recovery->save();
            $user->tokens()->delete();
            Auth::login($user);

            DB::commit();

            return response()->json([
                'success' => true,
                'token' => $user->createToken('token_' . time())->plainTextToken,
                'message' => "Votre mot de passe a été réinitialisé avec succès. Un email de confirmation vous a été envoyé."
            ]);
        } catch (\Exception $e) {
            Auth::logout($user);
            DB::rollBack();
            return response()->json(['success' => false, 'message' => "Une erreur est survenue, veuillez réessayer SVP."]);
        }
    }
}
