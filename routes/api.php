<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);

Route::middleware('userRole:admin')->group(function () {
    Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'getAll']);
    Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'create']);

    Route::put('/categories/{id}', [\App\Http\Controllers\CategoryController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/categories/{id}', [\App\Http\Controllers\CategoryController::class, 'delete'])->where(['id' => '[0-9]+']);
});

Route::middleware('userRole:user,admin')->group(function () {
    Route::get('/products', [\App\Http\Controllers\ProductController::class, 'getAll']);
    Route::post('/products', [\App\Http\Controllers\ProductController::class, 'create']);

    Route::put('/products/{id}', [\App\Http\Controllers\ProductController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/products/{id}', [\App\Http\Controllers\ProductController::class, 'delete'])->where(['id' => '[0-9]+']);
});
