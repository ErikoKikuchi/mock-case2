<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::factory()->create([
            'name'=>'テストユーザー',
            'email_verified_at' => now(),
            'role' => 'user',
        ]);
    }
    private function createAttendance(User $user)
    {
        $date = Carbon::today()->subDays(1);

        $attendance = Attendance::factory()->forDate($date)->create([
            'user_id'  => $user->id,
            'clock_in' => $date->copy()->setTime(9, 0),
            'clock_out' => $date->copy()->setTime(18, 0),
        ]);
        return $attendance;
    }

//勤怠詳細画面の「名前」がログインユーザーの氏名になっている
    public function test_user_can_see_name_on_detail_display()
    {
        $user = $this->createUser();
        $attendance=$this->createAttendance($user);
        $attendanceId = $attendance->id;
        $response = $this->actingAs($user)->get(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSee('テストユーザー');
    }

//勤怠詳細画面の「日付」が選択した日付になっている
    public function test_user_can_see_date_on_detail_display()
    {
        $user = $this->createUser();
        $attendance=$this->createAttendance($user);
        $attendanceId = $attendance->id;
        $response = $this->actingAs($user)->get(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSee(Carbon::today()->format('Y年'));
        $response->assertSee(Carbon::today()->subDays(1)->format('n月j日'));
    }


//「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している
    public function test_user_can_see_time_on_detail_display()
    {
        $user = $this->createUser();
        $attendance=$this->createAttendance($user);
        $attendanceId = $attendance->id;
        $response = $this->actingAs($user)->get(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSeeInOrder([
                '出勤・退勤',
                '09:00',
                '18:00',
            ]);
    }

//「休憩」にて記されている時間がログインユーザーの打刻と一致している
    public function test_user_can_see_break_time_on_detail_display()
    {
        $user = $this->createUser();
        $attendance=$this->createAttendance($user);
        $attendanceId = $attendance->id;
        BreakTime::create([
            'attendance_id' => $attendanceId,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
        $response = $this->actingAs($user)->get(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSeeInOrder([
                '休憩',
                '12:00',
                '13:00',
            ]);
    }
}
