<?php

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
Route::post('/refreshToken', [UserController::class, 'refreshToken']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [UserController::class, 'user']);
    Route::get('/getUser', [UserController::class, 'getUser']);
    Route::get('/showUser/{user}', [UserController::class, 'showUser']);
    Route::post('/updateUser/{user}', [UserController::class, 'update']);

});
