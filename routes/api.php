<?php

use App\Http\Controllers\API\EntityController;
use App\Http\Controllers\API\FuelpriceController;
use App\Http\Controllers\API\RateController;
use App\Http\Controllers\API\Structureprices;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('entity', EntityController::class);
    Route::resource('rate', RateController::class);
    Route::resource('structureprice', Structureprices::class);
    Route::resource('fuelprice', FuelpriceController::class);
});
