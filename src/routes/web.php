<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Contracts\LoginViewResponse;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRequestController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;


Route::middleware('guest')->group(function () {
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/admin/login',[AuthController::class,'adminlogin']);
});


Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/email/verify', function () {return view('auth.verify-email');
    })->name('verification.notice');
    Route::get('/redirect', function () {return redirect()->away(config('services.mailtrap.sandbox_url'));}) ->name('verification.open');
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back();
    })->middleware('throttle:6,1')->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('attendance.show');
    })->middleware(['auth'])->name('verification.verify');
});

Route::middleware(['auth', 'verified','role:user'])->group(function () {
    Route::get('/attendance',[AuthController::class,'show'])->name('attendance.show');
});

Route::middleware(['auth','role:admin'])->prefix('admin')->group(function () {
    Route::get('/attendance/list', [AuthController::class, 'index'])->name('attendance.list');
});