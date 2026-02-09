<?php

use App\Http\Controllers\API\AVGPriceController;
use App\Http\Controllers\API\DataController;
use App\Http\Controllers\API\DeliveryController;
use App\Http\Controllers\API\EntityController;
use App\Http\Controllers\API\FuelpriceController;
use App\Http\Controllers\API\PurchaseController;
use App\Http\Controllers\API\RateController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\Structureprices;
use App\Http\Controllers\API\TxStructure;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('dashboard', [DataController::class, 'dashboard'])->name('dashboard');
    Route::resource('entity', EntityController::class);
    Route::resource('rate', RateController::class);
    Route::resource('tx-structure', TxStructure::class)->only('index');
    Route::resource('structureprice', Structureprices::class);
    Route::resource('fuelprice', FuelpriceController::class);
    Route::resource('sale', SaleController::class);
    Route::resource('delivery', DeliveryController::class);
    Route::resource('purchase', PurchaseController::class);
    Route::resource('avgprice', AVGPriceController::class);

    Route::get('products-z', [DataController::class, 'product_z'])->name('extra.product_z');
});
