<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\userController;
use App\Http\Controllers\api\AuthController;

Route::get('deleted/users', [UserController::class, 'disabled']);
Route::resource('users', UserController::class);
Route::post('login', [AuthController::class, 'login']);