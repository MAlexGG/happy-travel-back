<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DestinationController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/destinations', [DestinationController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/destinations', [DestinationController::class, 'store']);
    Route::get('/destination/{id}', [DestinationController::class, 'show']);
    Route::delete('/destination/{id}', [DestinationController::class, 'destroy']);
    Route::get('/mydestinations', [DestinationController::class, 'getAllDestinationsByUser']);
    Route::post('/destinations/fav/{id}', [DestinationController::class, 'isFavorite']);
    Route::post('/destinations/notfav/{id}', [DestinationController::class, 'isNotFavorite']);
});
