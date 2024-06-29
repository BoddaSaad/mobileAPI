<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ItemsController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify', [AuthController::class, 'verify_email']);
Route::get('/reset', [AuthController::class, 'generate_code']);
Route::post('/reset', [AuthController::class, 'verify_code']);
Route::put('/reset', [AuthController::class, 'reset_password']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/categories', [CategoriesController::class, 'index']);
    Route::post('/items', [ItemsController::class, 'index']);
    Route::post('/favorites', [ItemsController::class, 'favorites']);
    Route::post('/favorites/add', [ItemsController::class, 'add_favorite']);
});
