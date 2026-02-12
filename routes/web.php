<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\LogisticsWebController;
use App\Http\Controllers\ProviderWebController;
use App\Http\Controllers\StateWebController;
use App\Http\Controllers\SudoWebController;
use App\Http\Middleware\APP\LogisticsMiddleware;
use App\Http\Middleware\APP\ProviderMiddleware;
use App\Http\Middleware\APP\StateMiddleware;
use App\Http\Middleware\APP\SudoMiddleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->name('api.login');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.logout');
});

Route::get('def', function () {
    $action = request('action');
    // if ('reset' == $action) {
    //     Artisan::call('migrate:refresh', ['--seed' => true]);
    // }
    if ('migrate' == $action) {
        Artisan::call('migrate', ['--seed' => true]);
    }

    $out = Artisan::output();
    dd($out);
});

Route::get('', function () {
    if (Auth::check()) {
        $role = auth()->user()->user_role;
        if ($role === 'sudo') {
            return redirect(route('sudo.home'));
        }
        if ($role === 'petrolier') {
            return redirect(route('provider.home'));
        }
        if ($role === 'logisticien') {
            return redirect(route('logistics.home'));
        }
        if ($role === 'etatique') {
            return redirect(route('state.home'));
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
            Route::prefix('accounting')->group(function () {
                Route::get('', 'accounting')->name('provider.accounting');
                Route::get('analyse', 'analyse')->name('provider.analyse');
                Route::get('claim', 'claim')->name('provider.claim');
                Route::get('delivery', 'delivery')->name('provider.delivery');
                Route::get('taxation', 'taxation')->name('provider.taxation');
            });
        });
    });

    Route::prefix('logistics')->middleware(LogisticsMiddleware::class)->group(function () {
        Route::controller(LogisticsWebController::class)->group(function () {
            Route::get('', 'home')->name('logistics.home');
            Route::get('sale', 'sale')->name('logistics.sale');
            Route::prefix('accounting')->group(function () {
                Route::get('', 'accounting')->name('logistics.accounting');
                Route::get('analyse', 'analyse')->name('logistics.analyse');
            });
        });
    });

    Route::prefix('state')->middleware(StateMiddleware::class)->group(function () {
        Route::controller(StateWebController::class)->group(function () {
            Route::get('', 'home')->name('state.home');
            Route::prefix('config')->group(function () {
                Route::get('', 'config')->name('state.config');
                Route::get('reconciliation/{entity}', 'reconciliation')->name('state.reconciliation');
                Route::get('avg-price', 'avg_price')->name('state.avg-price');
                Route::get('r-tx', 'real_tx')->name('state.real-tx');
                Route::get('s-tx', 'struct_tx')->name('state.struct-tx');
                Route::get('str-price', 'str_price')->name('state.str-price');
            });
            Route::prefix('{mode}/{entity}')
                ->whereIn('mode', ['view', 'edit'])
                ->group(function () {
                    Route::get('', 'apps')->name('state.apps');
                    Route::get('sale', 'sale')->name('state.sale');
                    Route::get('purchase', 'purchase')->name('state.purchase');
                    Route::prefix('accounting')->group(function () {
                        Route::get('', 'accounting')->name('state.accounting');
                        Route::get('analyse', 'analyse')->name('state.analyse');
                        Route::get('delivery', 'delivery')->name('state.delivery');
                        Route::get('claim', 'claim')->name('state.claim');
                        Route::get('taxation', 'taxation')->name('state.taxation');
                    });
                });
        });
    });
});
