<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Midtrans\Transaction;

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

Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
    Route::post('/refreshToken', 'refreshToken');
    Route::post('/getSession', 'getSession');
});

Route::middleware('auth:api')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/getUser', 'getUser');
        Route::get('/user', 'user');
        Route::get('/showUser/{user}', 'showUser');
        Route::post('/uploadPhoto/{user}', 'uploadPhoto');
        Route::post('/updateUser/{user}', 'update');
        Route::post('/updatePassword/{user}', 'updatePassword');
    });
    Route::controller(TransactionsController::class)->group(function () {
        Route::post('/createPayment', 'createPayment');
        Route::post('/showToken', 'showToken');
        Route::post('/tester', 'tester');
    });
});
