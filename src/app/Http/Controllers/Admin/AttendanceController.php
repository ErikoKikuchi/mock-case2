<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{

//管理者用勤怠詳細表示
public function show(Request $request, $id=null){
        $user=Auth::user();
        $userId = $request->query('user_id');
        if($id){
            $attendance=Attendance::findOrFail($id);
        }else{
            $date = $request->query('date');
            $userId = $request->query('user_id') ?? $user->id;
            $attendance = Attendance::firstOrCreate(
                ['user_id' => $userId, 'work_date' => $date]
            );
        }
        $breakTime=BreakTime::where('attendance_id',$attendance->id)->get();

        $attendanceRequest = AttendanceRequest::whereHas('requestItems', function($query) use ($attendance) {
            $query->where('attendance_id', $attendance->id);
        })->latest()->first();
        return view ('admin.attendance.detail',compact('attendance','attendanceRequest','breakTime'));
    }

}
