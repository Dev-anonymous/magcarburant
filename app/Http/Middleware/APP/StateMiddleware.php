<?php

namespace App\Http\Middleware\APP;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (@auth()->user()->user_role !== 'etatique') {
            Auth::guard('web')->logout();
            return redirect(route('login'));
        }
        return $next($request);
    }
}
