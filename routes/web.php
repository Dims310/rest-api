<?php

use App\Http\Controllers\AuthController;
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

Route::middleware('auth.jwt')->get('/user/myprofile', [AuthController::class, 'myprofile']);
Route::middleware('auth:api')->post('/user/logout', [AuthController::class, 'logout']);