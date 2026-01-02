<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ProviderWebController;
use App\Http\Controllers\SudoWebController;
use App\Http\Middleware\APP\ProviderMiddleware;
use App\Http\Middleware\APP\SudoMiddleware;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->name('api.login');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.logout');
});

Route::get('def', function () {
    $action = request('action');
    if ('reset' == $action) {
        Artisan::call('migrate:refresh', ['--seed' => true]);
    }
    if ('migrate' == $action) {
        Artisan::call('migrate');
    }

    foreach (Sale::all() as $e) {
        $e->update([
            'lata' => round($e->lata, 3),
            'l15' => round($e->l15, 3),
            'density' => round($e->density, 3),
        ]);
    }
    foreach (Purchase::all() as $e) {
        $e->update([
            'qtytm' => round($e->qtytm, 3),
            'qtym3' => round($e->qtym3, 3),
            'density' => round($e->density, 3),
        ]);
    }

    $out = Artisan::output();
    dd(@$out);
});

Route::get('', function () {
    if (Auth::check()) {
        $role = auth()->user()->user_role;
        if ($role === 'sudo') {
            return redirect(route('sudo.home'));
        }
        if ($role === 'provider') {
            return redirect(route('provider.home'));
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
    Route::prefix('provider')->middleware(ProviderMiddleware::class)->group(function () {
        Route::controller(ProviderWebController::class)->group(function () {
            Route::get('', 'home')->name('provider.home');
            Route::get('apps', 'apps')->name('provider.apps');
            Route::get('sale', 'sale')->name('provider.sale');
            Route::get('purchase', 'purchase')->name('provider.purchase');
            ROute::prefix('accounting')->group(function () {
                Route::get('', 'accounting')->name('provider.accounting');
                Route::get('analyse', 'analyse')->name('provider.analyse');
            });
        });
    });
});
