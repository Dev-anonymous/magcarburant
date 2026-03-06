<?php

use App\Http\Controllers\API\AccountingClosureController;
use App\Http\Controllers\API\AuditController;
use App\Http\Controllers\API\AVGPriceController;
use App\Http\Controllers\API\DataController;
use App\Http\Controllers\API\DeliveryController;
use App\Http\Controllers\API\EntityController;
use App\Http\Controllers\API\FuelpriceController;
use App\Http\Controllers\API\MiningsaleAPIController;
use App\Http\Controllers\API\PurchaseController;
use App\Http\Controllers\API\RateController;
use App\Http\Controllers\API\ReconciliationController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\StateFuelpriceController;
use App\Http\Controllers\API\StateRateController;
use App\Http\Controllers\API\StateStructurepriceController;
use App\Http\Controllers\API\StructurepriceController;
use App\Http\Controllers\API\TxStructure;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('dashboard', [DataController::class, 'dashboard'])->name('dashboard');
    Route::get('reconciliation', [ReconciliationController::class, 'reconciliation'])->name('reconciliation');
    Route::resource('audit', AuditController::class)->only(['index']);
    Route::resource('entity', EntityController::class);
    Route::resource('rate', RateController::class);
    Route::resource('staterate', StateRateController::class);
    Route::resource('tx-structure', TxStructure::class)->only('index');
    Route::resource('structureprice', StructurepriceController::class);
    Route::resource('statestructureprice', StateStructurepriceController::class);
    Route::resource('fuelprice', FuelpriceController::class);
    Route::resource('statefuelprice', StateFuelpriceController::class);
    Route::resource('sale', SaleController::class);
    Route::resource('miningsale', MiningsaleAPIController::class);
    Route::resource('delivery', DeliveryController::class);
    Route::resource('purchase', PurchaseController::class);
    Route::resource('avgprice', AVGPriceController::class);
    Route::resource('accountingclosure', AccountingClosureController::class)->only(['index', 'store', 'update']);

    Route::get('products-z', [DataController::class, 'product_z'])->name('extra.product_z');
});
