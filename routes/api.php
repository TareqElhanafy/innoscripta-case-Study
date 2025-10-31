<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\AuthorController;
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


// Authors
Route::prefix('/authors')->group(function () {
    Route::get('/', [AuthorController::class, 'index']);
    Route::get('/{id}', [AuthorController::class, 'show'])->where('id', '[0-9]+');
    Route::get('/{id}/articles', [AuthorController::class, 'articles'])->where('id', '[0-9]+');
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
