<?php

use App\Http\Controllers\SourceController;
use Illuminate\Support\Facades\Route;


// Sources
Route::prefix('/sources')->group(function () {
    Route::get('/', [SourceController::class, 'index']);
    Route::get('/{key}', [SourceController::class, 'show']);
});
