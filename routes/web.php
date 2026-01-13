<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ProviderWebController;
use App\Http\Controllers\SudoWebController;
use App\Http\Middleware\APP\ProviderMiddleware;
use App\Http\Middleware\APP\SudoMiddleware;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    // foreach (Sale::all() as $e) {
    //     $e->update([
    //         'lata' => round($e->lata, 3),
    //         'l15' => round($e->l15, 3),
    //         'density' => round($e->density, 3),
    //     ]);
    // }
    // foreach (Purchase::all() as $e) {
    //     $e->update([
    //         'qtytm' => round($e->qtytm, 3),
    //         'qtym3' => round($e->qtym3, 3),
    //         'density' => round($e->density, 3),
    //     ]);
    // }

    // $entities = [
    //     ['TOTAL', 'TOTAL ENERGIES SA', 'petrolier'],
    //     ['ENGEN', 'ENGEN RDC SA', 'petrolier'],
    //     ['COBIL', 'COBIL SA', 'petrolier'],
    //     ['SONAHYDROC', 'Société Nationale des Hydrocarbures du Congo', 'petrolier'],
    //     ['LEREXCOM', 'LEREXCOM', 'logisticien'],
    //     ['SEP CONGO', 'SEP CONGO', 'logisticien'],
    //     ['SPSA', 'SPSA COBIL', 'logisticien'],
    //     ['SOCIR', 'SOCIR', 'logisticien'],
    //     ['GPDPP', 'GPDPP', 'etatique'],
    //     ['FEC', 'Fédération des Entreprises du Congo', 'etatique'],
    //     ['MINECO', 'Ministère de l\'Économie', 'etatique'],
    //     ['PRIMATURE', 'Primature (Cabinet du Premier Ministre)', 'etatique'],
    //     ['PRESIDENCE', 'Présidence de la République', 'etatique'],
    //     ['MINHYD', 'Ministère des Hydrocarbures', 'etatique'],
    //     ['DGDA', 'Direction Générale des Douanes et Accises', 'etatique'],
    //     ['DGI', 'Direction Générale des Impôts', 'etatique'],
    //     ['AUTHENTIX', 'AUTHENTIX', 'etatique'],
    // ];

    // DB::statement("
    //             ALTER TABLE users
    //             MODIFY user_role ENUM('sudo', 'provider', 'petrolier', 'logisticien', 'etatique') NOT NULL
    //         ");
    // DB::transaction(function () use ($entities) {
    //     User::where(['user_role' => 'provider'])->update(['user_role' => 'sudo']);
    //     foreach ($entities as $el) {
    //         User::where(['name' => $el[0]])->update(['user_role' => $el[2]]);
    //     }
    // });
    // DB::statement("
    //             ALTER TABLE users
    //             MODIFY user_role ENUM('sudo', 'petrolier', 'logisticien', 'etatique') NOT NULL
    //         ");
    $out = Artisan::output();
    dd(@$out);
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
            });
        });
    });
});
