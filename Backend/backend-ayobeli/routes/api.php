<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group(['middleware' => 'api','prefix' => 'auth'], function ($router) {
Route::post('register', [AuthController::class, 'register']) ->name('register');
Route::post('login', [AuthController::class, 'login']) ->name('login');
Route::post('me', [AuthController::class, 'me']) ->name('me');

});