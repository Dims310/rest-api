<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/csrftoken', function() {return csrf_token(); });

Route::post('/user/register', [AuthController::class, 'register'])->name('register');
Route::post('/user/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth.jwt')->get('/user/myprofile', [AuthController::class, 'myprofile']);
Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);