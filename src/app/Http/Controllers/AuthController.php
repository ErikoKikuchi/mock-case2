<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;


class AuthController extends Controller
{

    public function register(RegisterRequest $request){
        $data=$request->validated();
        $data['password']=Hash::make($data['password']);
        $user=User::create($data);

        //メール認証
        event(new Registered($user));
        auth()->login($user);
        return redirect()->route('verification.notice');
    }
    public function show (Request $request){
        $user=Auth::user();

        $attendance=Attendance::where('user_id',$user->id)
        ->whereDate('work_date',today())
        ->with('breakTimes')
        ->first();

        return view('attendance.index', compact('user','attendance'));
    }
    public function index(Request $request){
        $user=Auth::user();
        return view('admin.attendance.index', compact('user'));
    }
    public function adminLogin(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'ログイン情報が登録されていません'])
                ->onlyInput('email');
        }

        $user = Auth::user();

        // 管理者以外が管理者ログインフォームから来た場合は拒否
        if ($user->role !== 'admin') {
            Auth::logout();
            return back()
                ->withErrors(['email' => 'ログイン情報が登録されていません'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->route('attendance.list');
    }
}