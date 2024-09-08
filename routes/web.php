<?php

use App\Http\Controllers\Api\v1\ServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/csrftoken', function() {return csrf_token(); });

Route::post('/user/register', [AuthController::class, 'register'])->name('register');
Route::post('/user/login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth.jwt', 'auth.admin']], function() {
    Route::get('/user', [UserController::class, 'getUsers'])->name('getUsers');
});

Route::group(['middleware' => ['auth.jwt']], function() {
    Route::get('/user/myprofile', [AuthController::class, 'myprofile']);
    Route::post('/user/logout', [AuthController::class, 'logout']);

    Route::post('/service/buy', [ServiceController::class, 'buyServices'])->name('buyServices');

    Route::get('/transaction', [TransactionController::class, 'getAllTransactions'])->name('getAllTransactions');
});