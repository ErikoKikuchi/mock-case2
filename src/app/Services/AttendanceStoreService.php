<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\Auth;

class AttendanceStoreService
{
    public function storeAttendance($action)
    {
        $user=Auth::user();
        $work_date=Carbon::today();

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
    }
}