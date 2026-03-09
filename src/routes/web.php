<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Contracts\LoginViewResponse;
use App\Http\Controllers\AuthController;



Route::get('/admin/login', function () {return app(LoginViewResponse::class);});

Route::middleware(['auth','admin'])->prefix('admin')->group(function () {
    Route::get('/attendance/list', [AuthController::class, 'index']);
});