<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRequestController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;


Route::middleware('guest')->group(function () {
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/admin/login',[AuthController::class,'adminLogin']);
    Route::get('admin/login', function () {return view('admin.auth.login');})->name('admin.login');
});


Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/admin/logout', [AuthController::class, 'adminLogout']);
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
    Route::get('/attendance/list',[AttendanceController::class,'index'])->name('users.attendance.list');
    Route::get('/attendance/detail/{id}',[AttendanceController::class,'show'])->name('users.attendance.detail');
    Route::get('/stamp_correction_request/list',[AttendanceRequestController::class,'index'])->name('users.request.list');
    Route::post('/attendance/store',[AttendanceController::class,'store'])->name('users.attendance.store');
});

Route::middleware(['auth','role:admin'])->prefix('admin')->group(function () {
    Route::get('/attendance/list', [AuthController::class, 'index'])->name('attendance.list');
    Route::get('/attendance/{id}',[AttendanceController::class,'adminShow'])->name('admin.attendance.detail');
    Route::get('/staff/list',[UserController::class,'index'])->name('staff.list');
    Route::get('/attendance/staff/{id}',[UserController::class,'show'])->name('each.staff.attendance');
    Route::get('/stamp_correction_request/list',[AttendanceRequestController::class,'adminIndex'])->name('request.list');
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}',[AttendanceRequestController::class,'adminShow'])->name('request.approve.detail');
    
});