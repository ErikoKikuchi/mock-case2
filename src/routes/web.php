<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Contracts\LoginViewResponse;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRequestController;


Route::middleware('guest')->group(function () {
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::get('/admin/login', function () {return app(LoginViewResponse::class);});
});

//Route::middleware(['auth','user'])->prefix('user')->group(function () {

//}

Route::middleware(['auth','admin'])->prefix('admin')->group(function () {
    Route::get('/attendance/list', [AuthController::class, 'index']);
});