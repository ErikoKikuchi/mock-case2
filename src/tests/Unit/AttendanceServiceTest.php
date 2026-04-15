<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use App\Models\User;

class AttendanceServiceTest extends TestCase
{
    private AttendanceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AttendanceService();
    }

    public function test_getCalendarData_月文字列を渡すと正しい前後月が返る(): void
    {
        $result = $this->service->getCalendarData('2025-01');

        $this->assertSame('2024-12', $result['previous']->format('Y-m'));
        $this->assertSame('2025-02', $result['next']->format('Y-m'));
        $this->assertSame('2025-01-01', $result['start']->toDateString());
        $this->assertSame('2025-01-31', $result['end']->toDateString());
    }

    public function test_getDailyData_日付文字列を渡すと正しい前後日付が返る(): void
    {
        $result =$this->service->getDailyData('2025-01-01');
        $this->assertSame('2024-12-31',$result['previous']->format('Y-m-d'));

        $result =$this->service->getDailyData('2025-12-31');
        $this->assertSame('2026-01-01',$result['next']->format('Y-m-d'));

        $result =$this->service->getDailyData('2024-02-28');
        $this->assertSame('2024-02-29',$result['next']->format('Y-m-d'));

        $result =$this->service->getDailyData('2024-03-01');
        $this->assertSame('2024-02-29',$result['previous']->format('Y-m-d'));
    }

    public function test_buildCalendar_マッピングの確認():void
    {
        $attendance = new Attendance();
        $attendance->work_date = Carbon::parse('2025-01-01');
        $attendance->user_id = 1;
        $attendance->clock_in=Carbon::parse('2025-01-01 09:00:00');
        $attendance->clock_out=Carbon::parse('2025-01-01 18:00:00');

        $period=CarbonPeriod::create('2025-01-01', '2025-01-02');
        $attendances = collect([$attendance]);
        $result = $this->service->buildCalendar($period, $attendances);

        $this->assertSame('2025-01-01', $result[0]['date']->toDateString());
        $this->assertSame($attendance, $result[0]['attendance']);
        $this->assertNull($result[1]['attendance']);
    }

    public function test_makeDailyCalendar_マッピングの確認():void
    {
        $attendance = new Attendance();
        $attendance->work_date = Carbon::parse('2025-01-01');
        $attendance->user_id = 1;
        $attendance->clock_in=Carbon::parse('2025-01-01 09:00:00');
        $attendance->clock_out=Carbon::parse('2025-01-01 18:00:00');
        $staff=new User();
        $staff->id = 1;
        $staff->name='テストユーザー';
        $staff->email='test@example.com';
        $staff->password='password';
        $staff->email_verified_at = Carbon::now();
        $staff->role = 'user';

        $attendances = collect([$attendance]);
        $staffs=collect([$staff]);
        $result=$this->service->makeDailyCalendar($staffs, $attendances);
        $this->assertSame('テストユーザー',$result[0]['staff']->name);
        $this->assertSame($attendance, $result[0]['attendance']);
    }
}