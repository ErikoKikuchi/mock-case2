<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class ApproveAttendanceService
{
    public function approveAttendance($data)
    {
        $user = $data['approved_by'];
        $attendanceRequest = $data['attendanceRequest'];
        $items = $attendanceRequest->requestItems; 

        foreach($items as $item){
            if($item->column_name === 'clock_in' || $item->column_name === 'clock_out'){
                Attendance::where('id', $item->attendance_id)
                    ->update([
                        $item->column_name => $item->after_value
                    ]);
            }else{
                if($item->break_id){
                    BreakTime::where('id', $item->break_id)
                        ->update([$item->column_name => $item->after_value]);
                } else {
                    if($item->column_name === 'break_start'){
                        BreakTime::create([
                            'attendance_id' => $item->attendance_id,
                            'break_start' => $item->after_value,
                        ]);
                    }
                }
            }
        }
        $attendanceRequest->update([
            'status' => 'approved',
            'approved_by'=>$user->id,
            'approved_at'=>Carbon::now(),
            ]);
    }
}