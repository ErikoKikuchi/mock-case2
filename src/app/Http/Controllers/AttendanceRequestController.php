<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceChangeRequest;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;

class AttendanceRequestController extends Controller
{
//申請一覧表示
    public function index(Request $request){
        $user=Auth::user();
        $tab=$request->query('tab','pending');

        $requestItems=AttendanceRequest::with(['attendance', 'user'])
            ->where('user_id',$user->id)
            ->where('status',$tab)
            ->get();

        return view('request.index',compact('user','tab','requestItems'));
    }
//申請
    public function create(AttendanceChangeRequest $request){
        $user=Auth::user();
        $attendance = Attendance::findOrFail($request->attendance_id);

        $attendanceRequest = AttendanceRequest::create([
            'user_id' => $attendance->user_id,
            'reason' => $request->reason,
            'status' => $user->role === 'admin' ? 'approved' : 'pending',
            'corrected_by' => $user->role === 'admin' ? $user->id : null,
            'attendance_id' => $attendance->id, 
        ]);
    //clock_inの申請
        $attendanceRequest->requestItems()->create([
            'attendance_id' => $attendance->id,
            'column_name' => 'clock_in',
            'before_value' => $attendance->clock_in,
            'after_value' => $request->clock_in,
        ]);
    // clock_outの申請
        $attendanceRequest->requestItems()->create([
            'attendance_id' => $attendance->id,
            'column_name' => 'clock_out',
            'before_value' => $attendance->clock_out,
            'after_value' => $request->clock_out,
        ]);
    //breakTimeの申請
        foreach($request->break_start as $index => $breakStart){
            $breakEnd = $request->break_end[$index] ?? null;
            $breakTime = BreakTime::where('attendance_id', $attendance->id)
                ->get()
                ->get($index);
            if($breakTime){
                $attendanceRequest->requestItems()->create([
                    'attendance_id' => $attendance->id,
                    'break_id' => $breakTime->id,
                    'column_name' => 'break_start',
                    'before_value' => $breakTime->break_start,
                    'after_value' => $breakStart,
                ]);
                $attendanceRequest->requestItems()->create([
                    'attendance_id' => $attendance->id,
                    'break_id' => $breakTime->id,
                    'column_name' => 'break_end',
                    'before_value' => $breakTime->break_end,
                    'after_value' => $breakEnd,
                ]);
            }
            }
        return $user->role === 'admin'
            ? redirect()->route('attendance.list', $attendance->user_id)
            ->with('message', '修正が完了しました')
            : redirect()
                    ->route('users.request.list')
                    ->with('message','変更申請が完了しました');
    }
//管理者用申請一覧
    public function adminIndex(Request $request)
    {
        $user = Auth::user();
        return view ('admin.request.index',compact('user'));
    }
//管理者用承認画面
    public function adminShow(Request $request)
    {
        $user = Auth::user();
        return view ('admin.request.approve',compact('user'));
    }
}