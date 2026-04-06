<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceRequest;
use App\Models\BreakTime;

class StoreAttendanceRequestService
{
    //申請共通部
    public function storeAttendanceRequest($request, $attendance)
    {
        $user=Auth::user();
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
            if(empty($breakStart)){
                continue;
            }
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
            }else{
                $attendanceRequest->requestItems()->create([
                    'attendance_id' => $attendance->id,
                    'break_id' => null,
                    'column_name' => 'break_start',
                    'before_value' => null,
                    'after_value' => $breakStart,
                ]);
                $attendanceRequest->requestItems()->create([
                    'attendance_id' => $attendance->id,
                    'break_id' => null,
                    'column_name' => 'break_end',
                    'before_value' => null,
                    'after_value' => $breakEnd,
                ]);
            }
        }
        return [
            'attendanceRequest'=>$attendanceRequest,
        ];
    }
}