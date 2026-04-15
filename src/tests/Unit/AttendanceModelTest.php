<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceModelTest extends TestCase
{
    public function test_totalBreakMinutes_休憩時間の合計():void
    {
        $attendance = new Attendance();
        $attendance->work_date = Carbon::today();
        $attendance->user_id = 1;
        $attendance->clock_in=Carbon::parse('2025-01-01 09:00:00');
        $attendance->clock_out=Carbon::parse('2025-01-01 18:00:00');

        $breakTime =new BreakTime();
        $breakTime->attendance_id=$attendance->id;
        $breakTime->break_start=Carbon::parse('2025-01-01 12:00:00');
        $breakTime->break_end=Carbon::parse('2025-01-01 13:00:00');

        $attendance->setRelation('breakTimes', collect([$breakTime]));

        $result = $attendance->total_break_minutes;

        $this->assertSame(60,$result);
    }

    public function test_totalBreakMinutes_休憩なし():void
    {
        $attendance = new Attendance();
        $attendance->work_date = Carbon::today();
        $attendance->user_id = 1;
        $attendance->clock_in=Carbon::parse('2025-01-01 09:00:00');
        $attendance->clock_out=Carbon::parse('2025-01-01 18:00:00');

        $breakTime =new BreakTime();
        $breakTime->attendance_id=$attendance->id;
        $breakTime->break_start=null;
        $breakTime->break_end=null;

        $attendance->setRelation('breakTimes', collect([$breakTime]));

        $result = $attendance->total_break_minutes;

        $this->assertSame(0,$result);
    }

    public function test_totalBreakMinutes_休憩終わり未申請():void
    {
        $attendance = new Attendance();
        $attendance->work_date = Carbon::today();
        $attendance->user_id = 1;
        $attendance->clock_in=Carbon::parse('2025-01-01 09:00:00');
        $attendance->clock_out=Carbon::parse('2025-01-01 18:00:00');

        $breakTime1 =new BreakTime();
        $breakTime1->attendance_id=$attendance->id;
        $breakTime1->break_start=Carbon::parse('2025-01-01 12:00:00');
        $breakTime1->break_end=Carbon::parse('2025-01-01 12:30:00');

        $breakTime2 =new BreakTime();
        $breakTime2->attendance_id=$attendance->id;
        $breakTime2->break_start=Carbon::parse('2025-01-01 15:30:00');
        $breakTime2->break_end=null;

        $attendance->setRelation('breakTimes', collect([$breakTime1,$breakTime2]));

        $result = $attendance->total_break_minutes;

        $this->assertSame(30,$result);
    }

    public function test_totalBreakMinutes_休憩3回():void
    {
        $attendance = new Attendance();
        $attendance->work_date = Carbon::today();
        $attendance->user_id = 1;
        $attendance->clock_in=Carbon::parse('2025-01-01 09:00:00');
        $attendance->clock_out=Carbon::parse('2025-01-01 18:00:00');

        $breakTime1 =new BreakTime();
        $breakTime1->attendance_id=$attendance->id;
        $breakTime1->break_start=Carbon::parse('2025-01-01 12:00:00');
        $breakTime1->break_end=Carbon::parse('2025-01-01 12:30:00');

        $breakTime2 =new BreakTime();
        $breakTime2->attendance_id=$attendance->id;
        $breakTime2->break_start=Carbon::parse('2025-01-01 15:30:00');
        $breakTime2->break_end=Carbon::parse('2025-01-01 16:00:00');

        $breakTime3 =new BreakTime();
        $breakTime3->attendance_id=$attendance->id;
        $breakTime3->break_start=Carbon::parse('2025-01-01 17:30:00');
        $breakTime3->break_end=Carbon::parse('2025-01-01 17:45:00');

        $attendance->setRelation('breakTimes', collect([$breakTime1,$breakTime2,$breakTime3]));

        $result = $attendance->total_break_minutes;

        $this->assertSame(75,$result);
    }

    public function test_totalWorkMinutes_勤務時間の合計():void
    {
        $attendance = new Attendance();
        $attendance->work_date = Carbon::parse('2025-01-01');
        $attendance->user_id = 1;
        $attendance->clock_in=Carbon::parse('2025-01-01 09:00:00');
        $attendance->clock_out=Carbon::parse('2025-01-01 18:00:00');

        $breakTime =new BreakTime();
        $breakTime->attendance_id=$attendance->id;
        $breakTime->break_start=Carbon::parse('2025-01-01 12:00:00');
        $breakTime->break_end=Carbon::parse('2025-01-01 13:00:00');

        $attendance->setRelation('breakTimes', collect([$breakTime]));

        $result = $attendance->total_work_minutes;
        $this->assertSame(480,$result);
    }
    public function test_totalWorkMinutes_勤務時間の打刻漏れ():void
    {
        $attendance = new Attendance();
        $attendance->work_date = Carbon::parse('2025-01-01');
        $attendance->user_id = 1;
        $attendance->clock_in=null;
        $attendance->clock_out=Carbon::parse('2025-01-01 18:00:00');

        $breakTime =new BreakTime();
        $breakTime->attendance_id=$attendance->id;
        $breakTime->break_start=Carbon::parse('2025-01-01 12:00:00');
        $breakTime->break_end=Carbon::parse('2025-01-01 13:00:00');

        $attendance->setRelation('breakTimes', collect([$breakTime]));

        $result = $attendance->total_work_minutes;
        $this->assertNull($result);
    }
}