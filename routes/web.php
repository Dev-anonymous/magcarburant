<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\LogisticsWebController;
use App\Http\Controllers\ProviderWebController;
use App\Http\Controllers\StateWebController;
use App\Http\Controllers\SudoWebController;
use App\Http\Controllers\WebController;
use App\Http\Middleware\APP\LogisticsMiddleware;
use App\Http\Middleware\APP\ProviderMiddleware;
use App\Http\Middleware\APP\StateMiddleware;
use App\Http\Middleware\APP\SudoMiddleware;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->name('api.login');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.logout');
});

Route::get('def', function () {
    $action = request('action');
    if ('migrate' == $action) {
        Artisan::call('migrate', ['--seed' => true]);
    }
    $out = Artisan::output();
    dd($out);
});

Route::get('', function () {
    if (Auth::check()) {
        $user = request()->user();
        $role = $user->user_role;
        $route = null;
        $parent = $user->user;

        if ($role === 'sudo') {
            $route = 'sudo.home';
        }
        if ($role === 'petrolier' || $parent && $parent->user_role === 'petrolier' && $role === 'utilisateur') {
            $route = 'provider.home';
        }
        if ($role === 'logisticien' || $parent && $parent->user_role === 'logisticien' && $role === 'utilisateur') {
            $route = 'logistics.home';
        }
        if ($role === 'etatique' || $parent && $parent->user_role === 'etatique' && $role === 'utilisateur') {
            $route = 'state.home';
        }

        if ($route) {
            return redirect()->route($route);
        }
    }
    return view('login');
})->name('login');

Route::prefix('recovery')->group(function () {
    Route::get('', [WebController::class, 'recovery'])->name('recovery');
    Route::post('verify', [WebController::class, 'recovery_verify'])->name('recovery.verify');
    Route::post('reset', [WebController::class, 'recovery_reset'])->name('recovery.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('app-logs', [WebController::class, 'applogs'])->name('applogs');
    Route::get('roles', [WebController::class, 'roles'])->name('roles');
    Route::get('users', [WebController::class, 'users'])->name('users');

    Route::prefix('super-admin')->middleware(SudoMiddleware::class)->group(function () {
        Route::controller(SudoWebController::class)->group(function () {
            Route::get('', 'home')->name('sudo.home');
            Route::get('provider', 'provider')->name('sudo.provider');
        });
    });
    Route::prefix('provider')->middleware(ProviderMiddleware::class)->group(function () {
        Route::controller(ProviderWebController::class)->group(function () {
            Route::get('', 'home')->name('provider.home');
            Route::get('dash', 'dash')->name('provider.dash');
            Route::get('apps', 'apps')->name('provider.apps');
            Route::get('sale', 'sale')->name('provider.sale');
            Route::get('mining-sale', 'mining_sale')->name('provider.mining-sale');
            Route::get('purchase', 'purchase')->name('provider.purchase');
            Route::get('security-stock', 'security_stock')->name('provider.security-stock');
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
            Route::get('dash', 'dash')->name('logistics.dash')->middleware('permMdlw:Tableau de bord - Lire');
            Route::get('sale', 'sale')->name('logistics.sale')->middleware('permMdlw:Vente - Lire');
            // Route::get('mining-sale', 'mining_sale')->name('logistics.mining-sale')->middleware('permMdlw:Vente - Lire');
            Route::prefix('accounting')->group(function () {
                Route::get('', 'accounting')->name('logistics.accounting')->middleware('permMdlw:Comptabilité - Lire');
                Route::get('analyse', 'analyse')->name('logistics.analyse')->middleware('permMdlw:Bilan manque à gagner - Lire');
            });
        });
    });

    Route::prefix('state')->middleware(StateMiddleware::class)->group(function () {
        Route::controller(StateWebController::class)->group(function () {
            Route::get('', 'home')->name('state.home');
            Route::get('dash', 'dash')->name('state.dash');
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
                    Route::get('security-stock', 'security_stock')->name('state.security-stock');
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
