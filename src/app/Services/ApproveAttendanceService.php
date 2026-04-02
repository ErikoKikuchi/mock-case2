<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\BreakTime;

class ApproveAttendanceService
{
    public function approveAttendance($data)
    {
        $attendanceRequest = $data['attendanceRequest'];
        $items = $attendanceRequest->requestItems; 

        foreach($items as $item){
            if($item->column_name === 'clock_in' || $item->column_name === 'clock_out'){
                Attendance::where('id', $item->attendance_id)
                    ->update([
                        $item->column_name => $item->after_value
                    ]);
            }else{
                BreakTime::where('id', $item->break_id)
                    ->update([
                        $item->column_name => $item->after_value
                    ]);
            }
        }
    }
}