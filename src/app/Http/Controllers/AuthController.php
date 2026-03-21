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
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;


class AuthController extends Controller
{
//登録関係
    public function register(RegisterRequest $request){
        $data=$request->validated();
        $data['password']=Hash::make($data['password']);
        $user=User::create($data);

        //メール認証
        event(new Registered($user));
        auth()->login($user);
        return redirect()->route('verification.notice');
    }
//ログイン関係
    public function login(LoginRequest $request)
        {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return back()
                    ->withErrors(['email' => 'ログイン情報が登録されていません'])
                    ->onlyInput('email');
            }

            // 認可チェック
            if (Gate::denies('user-only')) {
                Auth::logout();
                return back()
                    ->withErrors(['email' => 'ログイン情報が登録されていません'])
                    ->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->route('attendance.show');
        }

    public function adminLogin(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'ログイン情報が登録されていません'])
                ->onlyInput('email');
        }

        // 認可チェック
        if (Gate::denies('admin-only')) {
            Auth::logout();
            return back()
                ->withErrors(['email' => 'ログイン情報が登録されていません'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->route('attendance.list');
    }

//ログアウト関係

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function adminLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

//ユーザー勤怠登録画面表示
    public function show (Request $request){
        $user=Auth::user();

        $attendance=Attendance::where('user_id',$user->id)
        ->whereDate('work_date',today())
        ->with('breakTimes')
        ->first();

        if(!$attendance){
            $status='勤務外';
            $attendanceButtons=['出勤'];
        }else{
            $status=$attendance->status;
            $attendanceButtons=$attendance->attendanceButton;
        }
        $date=Carbon::now()->locale('ja');

        return view('attendance.index', compact('user','attendance','status','date','attendanceButtons'));
    }
    public function index(Request $request){
        $user=Auth::user();
        return view('admin.attendance.index', compact('user'));
    }


}