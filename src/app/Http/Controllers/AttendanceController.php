<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AttendanceRequest  as RequestModel;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
//勤怠登録
    public function store(RequestModel $request){
        if (Gate::denies('user-only')) {
            abort(403);
        }

        $user=Auth::user();
        $work_date=Carbon::now();
        $action=$request->action;

        if($action ==='出勤'){
            Attendance::firstOrCreate([
                'user_id'=>$user->id,
                'work_date'=>$work_date],
                ['clock_in' => now()]);
        }elseif($action ==='退勤'){
            Attendance::where('user_id',$user->id)
            ->whereDate('work_date',today())
            ->update(['clock_out'=>now()]);
        }elseif($action ==='休憩入'){
            $attendance=Attendance::where('user_id',$user->id)
            ->whereDate('work_date',today())
            ->first();
            BreakTime::create([
                'attendance_id'=>$attendance->id,
                'break_start' => now(),
            ]);
        }else{
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('work_date', today())
                ->first();
            BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('break_end')
                ->first()
                ->update(['break_end' => now()]);
        }
        return redirect()->route('attendance.show');
    }

//勤怠一覧表示
    public function index(Request $request){
        if (Gate::denies('user-only')) {
            abort(403);
        }
        $user=Auth::user();

        $month=$request->query('month');
        $date=$month ?Carbon::parse($month)->locale('ja'):Carbon::now()->locale('ja');

        $previous=$date->copy()->subMonth();
        $next=$date->copy()->addMonth();

        $start=$date->copy()->startOfMonth();
        $end=$date->copy()->endOfMonth();

        $monthlyAttendances=Attendance::where('user_id',$user->id)
        ->WhereBetween('work_date',[$start,$end])
        ->get();

        return view('attendance.index',compact('user','date','monthlyAttendances','previous','next'));
    }
//勤怠詳細表示
    public function show(Request $request, $id){
        if (Gate::denies('user-only')) {
            abort(403);
        }
        $user=Auth::user();
        $attendance=Attendance::find($id);
        $breakTime=BreakTime::where('attendance_id',$attendance->id)->get();

        $attendanceRequest = AttendanceRequest::whereHas('requestItems', function($query) use ($attendance) {
            $query->where('attendance_id', $attendance->id);
        })->latest()->first();
        return view ('attendance.detail',compact('user','attendance','attendanceRequest','breakTime'));
    }
}
