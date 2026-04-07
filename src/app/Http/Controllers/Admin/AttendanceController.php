<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceDetailRequest;

class AttendanceController extends Controller
{

//管理者用勤怠詳細表示
    public function show(AttendanceDetailRequest $request, $id=null){
        $user=Auth::user();
        $userId = $request->query('user_id');
        $date=$request->query('date');

        $attendance=Attendance::findOrResolveByDate($id, $userId,$date);

        $breakTime=BreakTime::where('attendance_id',$attendance->id)->get();

        $attendanceRequest = AttendanceRequest::latestByAttendance($attendance);

        return view ('admin.attendance.detail',compact('attendance','attendanceRequest','breakTime'));
    }

}
