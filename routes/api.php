<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ImageUploadersController;
use App\Http\Controllers\MusicsController;
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
});

Route::middleware('auth:api')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('/getUser', 'getUser');
            Route::get('/user', 'user');
            Route::get('/showUser/{user}', 'showUser');
            Route::post('/refreshToken', 'refreshToken');
            Route::post('/getSession', 'getSession');
            Route::post('/uploadPhoto/{user}', 'uploadPhoto');
            Route::post('/updateUser/{user}', 'update');
            Route::post('/updatePassword/{user}', 'updatePassword');
        });
    });
    Route::controller(TransactionsController::class)->group(function () {
        Route::prefix('transaction')->group(function () {
            Route::post('/createPayment', 'createPayment');
            Route::post('/showToken', 'showToken');
            Route::get('/historyByUser', 'historyByUser');
        });
    });
    Route::controller(ProjectsController::class)->group(function () {
        Route::prefix('project')->group(function () {
            Route::post('/storeOrUpdate', 'storeOrUpdate');
            Route::post('/getById/{id}', 'getById');
            Route::post('/update/{id}', 'update');
            Route::post('/getByUserId', 'getByUserId');
        });
    });
    Route::controller(ImageUploadersController::class)->group(function () {
        Route::prefix('images')->group(function () {
            Route::post('/createImage', 'createImage');
        });
    });
    Route::controller(MusicsController::class)->group(function () {
        Route::prefix('musics')->group(function () {
            Route::get('/getMusics', 'getAll');
            Route::post('getById/{id}', 'getById');
            Route::post('/createMusics', 'create');
            Route::post('/update/{id}', 'update');
            Route::post('/delete/{id}', 'destroy');
        });
    });
});
