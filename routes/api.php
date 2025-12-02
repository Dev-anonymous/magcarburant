<?php

use App\Http\Controllers\API\DataController;
use App\Http\Controllers\API\EntityController;
use App\Http\Controllers\API\FuelpriceController;
use App\Http\Controllers\API\PurchaseController;
use App\Http\Controllers\API\RateController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\Structureprices;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('entity', EntityController::class);
    Route::resource('rate', RateController::class);
    Route::resource('structureprice', Structureprices::class);
    Route::resource('fuelprice', FuelpriceController::class);
    Route::resource('sale', SaleController::class);
    Route::resource('purchase', PurchaseController::class);
    Route::get('dashboard', [DataController::class, 'dashboard'])->name('dashboard');
    // Route::get('price-structure', [DataController::class, 'pricestructure'])->name('pricestructure');
});
