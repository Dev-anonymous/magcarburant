<?php

namespace App\Http\Middleware\APP;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProviderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ok = false;
        $user = $request->user();
        if ($user) {
            $parent = $user->user;
            if ($user->user_role === 'petrolier' || $parent && $parent->user_role === 'petrolier' && $user->user_role === 'utilisateur') {
                $ok = true;
            }
        }
        if (!$ok) {
            Auth::guard('web')->logout();
            return redirect(route('login'));
        }
        return $next($request);
    }
}
