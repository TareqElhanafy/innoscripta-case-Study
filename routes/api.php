<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SourceController;
use Illuminate\Support\Facades\Route;


// user authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


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


// Articles
Route::prefix('/articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/{id}', [ArticleController::class, 'show']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // user preferences
    Route::prefix('/user/preferences')->group(function () {
        Route::get('/', [UserPreferenceController::class, 'show']);
        Route::put('/', [UserPreferenceController::class, 'update']);
    });
});
