<?php

use App\Http\Controllers\AssetsEditorController;
use Midtrans\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RsvpController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MusicsController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\GuestListController;
use App\Http\Controllers\InvitationsController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\ImageUploadersController;

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
Route::middleware('api')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::post('/logout', 'logout');
    });
    Route::controller(InvitationsController::class)->group(function () {
        Route::prefix('invitations')->group(function () {
            Route::post('/getInvitation/{id}', 'getInvitation');
            Route::post('/getTransaction/{id}', 'getTransaction');
        });
    });
    Route::controller(RsvpController::class)->group(function () {
        Route::prefix('rsvp')->group(function () {
            Route::post('/create', 'create');
        });
    });
});

Route::middleware('auth:api')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('/getUser', 'getUser');
            Route::get('/user', 'user');
            Route::post('/showUser/{user}', 'showUser');
            Route::post('/refreshToken', 'refreshToken');
            Route::post('/create', 'create');
            Route::post('/getSession', 'getSession');
            Route::post('/uploadPhoto/{user}', 'uploadPhoto');
            Route::post('/updateUser/{user}', 'update');
            Route::post('/updatePassword/{user}', 'updatePassword');
            Route::post('/updateAdmin/{user}', 'updateAdmin');
            Route::post('/delete/{id}', 'destroy');
        });
    });
    Route::controller(TransactionsController::class)->group(function () {
        Route::prefix('transaction')->group(function () {
            Route::post('/createPayment', 'createPayment');
            Route::post('/showToken', 'showToken');
            Route::get('/historyByUser', 'historyByUser');
            Route::get('/transactionExport', 'transactionExport');
            Route::get('/getMonthlyTransaction', 'getMonthlyTransaction');
            Route::get('/getCardDashboard', 'getCardDashboard');
        });
    });
    Route::controller(ProjectsController::class)->group(function () {
        Route::prefix('project')->group(function () {
            Route::post('/storeOrUpdate', 'storeOrUpdate');
            Route::post('/getById/{id}', 'getById');
            Route::post('/update/{id}', 'update');
            Route::post('/getByUserId', 'getByUserId');
            Route::post('/destroy/{id}', 'destroy');
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
    Route::controller(InvitationsController::class)->group(function () {
        Route::prefix('invitations')->group(function () {
            Route::get('/getAll', 'getAll');
            Route::post('/create', 'create');
            Route::post('/getByProject/{id}', 'getByProject');
            Route::post('/editClipbord', 'editClipbord');
            Route::post('/delete/{id}', 'destroy');
        });
    });
    Route::controller(GuestListController::class)->group(function () {
        Route::prefix('guest')->group(function () {
            Route::post('/create', 'create');
            Route::post('/import', 'import');
            Route::get('/getById/{id}', 'getById');
            Route::post('/attendance', 'attendance');
            Route::get('/getByIdGuest/{id}', 'getByIdGuest');
            Route::post('/update/{id}', 'update');
            Route::post('/delete/{id}', 'destroy');
            Route::get('/guestStats/{id}', 'guestStats');
        });
    });
    Route::controller(RsvpController::class)->group(function () {
        Route::prefix('rsvp')->group(function () {
            Route::get('/getById/{id}', 'getById');
        });
    });
    Route::controller(WhatsAppController::class)->group(function () {
        Route::prefix('whatsapp')->group(function () {
            Route::post('/runWhatsAppBot', 'runWhatsAppBot');
        });
    });
    Route::controller(AssetsEditorController::class)->group(function () {
        Route::prefix('assets')->group(function () {
            Route::get('/getAssets', 'getAll');
            Route::post('getById/{id}', 'getById');
            Route::post('/createAssets', 'create');
            Route::post('/update/{id}', 'update');
            Route::post('/delete/{id}', 'destroy');
        });
    });
});
