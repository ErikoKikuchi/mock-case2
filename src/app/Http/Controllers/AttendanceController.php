<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AttendanceRequest  as RequestModel;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceController extends Controller
{
//勤怠登録
    public function store(RequestModel $request){

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
        $user=Auth::user();

        $month=$request->query('month');
        $date=$month ?Carbon::parse($month)->locale('ja'):Carbon::now()->locale('ja');

        $previous=$date->copy()->subMonth();
        $next=$date->copy()->addMonth();

        $start=$date->copy()->startOfMonth();
        $end=$date->copy()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        $monthlyAttendances=Attendance::where('user_id',$user->id)
        ->WhereBetween('work_date',[$start,$end])
        ->get();

        $calendar = collect();
        foreach($period as $date){
            $calendar->push([
                'date' => $date,
                'attendance' => $monthlyAttendances->first(fn($a) => $a->work_date->toDateString() === $date->toDateString())
            ]);
        }


        return view('attendance.index',compact('user','date','monthlyAttendances','previous','next','calendar'));
    }
//勤怠詳細表示
    public function show(Request $request, $id=null){
        $user=Auth::user();
        if($id){
            $attendance=Attendance::findOrFail($id);
        }else{
            $date = $request->query('date');
            $userId = $request->query('user_id') ?? $user->id;
            $attendance = Attendance::firstOrCreate(
                ['user_id' => $user->id, 'work_date' => $date]
            );
        }
        $breakTime=BreakTime::where('attendance_id',$attendance->id)->get();

        $attendanceRequest = AttendanceRequest::whereHas('requestItems', function($query) use ($attendance) {
            $query->where('attendance_id', $attendance->id);
        })->latest()->first();
        return view ('attendance.detail',compact('user','attendance','attendanceRequest','breakTime'));
    }
}
