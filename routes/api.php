<?php

use App\Http\Controllers\API\EntityController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('entity', EntityController::class);
});
