<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AttendanceRequestController as AdminAttendanceRequestController;
use App\Http\Controllers\User\AttendanceController as UserAttendanceController;
use App\Http\Controllers\User\AttendanceRequestController as UserAttendanceRequestController;
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
    Route::get('/stamp_correction_request/list',[UserAttendanceRequestController::class,'index'])->name('users.request.list');
    Route::post('/attendance/store',[UserAttendanceController::class,'store'])->name('users.attendance.store');
    Route::get('/attendance/list',[UserAttendanceController::class,'index'])->name('users.attendance.list');
    Route::get('/attendance/detail/{id?}',[UserAttendanceController::class,'show'])->name('users.attendance.detail');
    Route::post('/attendance/request/send',[UserAttendanceRequestController::class,'create'])->name('users.attendance.request');
});

Route::middleware(['auth','role:admin'])->prefix('admin')->group(function () {
    Route::get('/attendance/list', [AuthController::class, 'index'])->name('attendance.list');
    Route::get('/attendance/{id?}',[AdminAttendanceController::class,'show'])->name('admin.attendance.detail');
    Route::get('/staff/list',[UserController::class,'index'])->name('staff.list');
    Route::get('/attendance/staff/{id}',[UserController::class,'show'])->name('each.staff.attendance');
    Route::get('/stamp_correction_request/list',[AdminAttendanceRequestController::class,'index'])->name('request.list');
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}',[AdminAttendanceRequestController::class,'show'])->name('request.approve.detail');
    Route::post('/attendance/request/send',[AdminAttendanceRequestController::class,'store'])->name('admin.attendance.request');
    Route::get('/csv/{id}', [UserController::class, 'exportCsv'])->name('admin.csv');
    Route::post('/attendance/update',[AdminAttendanceRequestController::class,'update'])->name('update.attendance');
});