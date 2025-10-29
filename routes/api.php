<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SourceController;
use Illuminate\Support\Facades\Route;


// Sources
Route::prefix('/sources')->group(function () {
    Route::get('/', [SourceController::class, 'index']);
    Route::get('/{key}', [SourceController::class, 'show']);
});


// Categories
Route::prefix('/categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{slug}', [CategoryController::class, 'show']);
});
