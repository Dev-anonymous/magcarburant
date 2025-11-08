<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\SudoWebController;
use App\Http\Middleware\APP\SudoMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->name('api.login');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.logout');
});

Route::get('', function () {
    if (Auth::check()) {
        $role = auth()->user()->user_role;
        if ($role === 'sudo') {
            return redirect(route('sudo.home'));
        }
    }
    return view('login');
})->name('login');


Route::middleware('auth')->group(function () {
    Route::prefix('super-admin')->middleware(SudoMiddleware::class)->group(function () {
        Route::controller(SudoWebController::class)->group(function () {
            Route::get('', 'home')->name('sudo.home');
            Route::get('provider', 'provider')->name('sudo.provider');
        });
    });
});
