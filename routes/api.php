<?php

use App\Http\Controllers\API\EntityController;
use App\Http\Controllers\API\RateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('entity', EntityController::class);
    Route::resource('rate', RateController::class);
});
