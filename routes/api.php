<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UserController;
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
    Route::post('/category', [CategoriesController::class, 'show']);
    Route::post('/items', [ItemsController::class, 'index']);
    Route::post('/item', [ItemsController::class, 'show']);
    Route::post('/favorites', [ItemsController::class, 'favorites']);
    Route::post('/favorites/add', [ItemsController::class, 'add_favorite']);
    Route::post('/favorites/delete', [ItemsController::class, 'delete_favorite']);
    Route::post('/favorites/delete/all', [ItemsController::class, 'delete_all_favorites']);
    Route::post('/schedule', [BookingsController::class, 'get_available_hours']);
    Route::post('/booking', [BookingsController::class, 'booking']);
    Route::post('/user/bookings', [BookingsController::class, 'my_booking']);
    Route::post('/rate', [RatingController::class, 'rate']);
    Route::post('/ratings', [RatingController::class, 'show_ratings']);
    Route::post('/profile', [UserController::class, 'update']);
});
