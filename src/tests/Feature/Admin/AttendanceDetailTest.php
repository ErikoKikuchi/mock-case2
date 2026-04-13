<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);
    }
    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }
    private function createAttendanceWithBreakTime(User $user)
    {
        $date = Carbon::today();

        $attendance= Attendance::factory()->forDate($date)->create([
            'user_id'  => $user->id,
            'clock_in' => $date->copy()->setTime(9, 0),
            'clock_out' => $date->copy()->setTime(18, 0),
        ]);

        BreakTime::create([
        'attendance_id' => $attendance->id,
        'break_start'  => $date->copy()->setTime(12, 0),
        'break_end' => $date->copy()->setTime(13, 0),
    ]);
        return $attendance;
    }
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

//勤怠詳細画面に表示されるデータが選択したものになっている
    public function test_selected_data_can_see_at_attendance_detail()
    {
        $user = $this->createUser();
        $attendance=$this->createAttendanceWithBreakTime($user);
        $attendanceId = $attendance->id;
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('attendance.list'));
        $response->assertSee($user->name);
        $response->assertSee('09:00');
        $response = $this->actingAs($admin)->get(route('admin.attendance.detail'),['id'=>$attendanceId]);
        $response->assertSee($user->name);
        $response->assertSee('09:00');
    }

//出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_error_message_is_displayed_if_clockIn_is_after_clockOut()
    {
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get('/admin/attendance?date=' . Carbon::today()->format('Y-m-d') . '&user_id=' . $user->id);
        $response=$this->actingAs($admin)->post(route('admin.attendance.request'),[
            'attendance_id' => Attendance::where('user_id', $user->id)->first(),
            'clock_in'=>'19:00',
            'clock_out'=>'18:00',
            'reason'=>'テスト',
            'status'=>'approved']);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['clock_out'=>'出勤時間もしくは退勤時間が不適切な値です']);
    }

//休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_error_message_is_displayed_if_breakStart_is_after_clockOut()
    {
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get('/admin/attendance?date=' . Carbon::today()->format('Y-m-d') . '&user_id=' . $user->id);
        $response=$this->actingAs($admin)->post(route('admin.attendance.request'),[
            'attendance_id' => Attendance::where('user_id', $user->id)->first(),
            'clock_in'=>'09:00',
            'clock_out'=>'18:00',
            'break_start'=>['19:00'],
            'break_end'=>['13:00'],
            'reason'=>'テスト',
            'status'=>'approved']);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['break_start.0'=>'休憩時間が不適切な値です']);
    }

//休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_error_message_is_displayed_if_breakEnd_is_after_clockOut()
    {
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get('/admin/attendance?date=' . Carbon::today()->format('Y-m-d') . '&user_id=' . $user->id);
        $response=$this->actingAs($admin)->post(route('admin.attendance.request'),[
            'attendance_id' => Attendance::where('user_id', $user->id)->first(),
            'clock_in'=>'09:00',
            'clock_out'=>'18:00',
            'break_start'=>['12:00'],
            'break_end'=>['19:00'],
            'reason'=>'テスト',
            'status'=>'approved']);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['break_end.0'=>'休憩時間もしくは退勤時間が不適切な値です']);
    }

//備考欄が未入力の場合のエラーメッセージが表示される
    public function test_error_message_is_displayed_if_reason_is_null()
    {
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get('/admin/attendance?date=' . Carbon::today()->format('Y-m-d') . '&user_id=' . $user->id);
        $response=$this->actingAs($admin)->post(route('admin.attendance.request'),[
            'attendance_id' => Attendance::where('user_id', $user->id)->first(),
            'clock_in'=>'09:00',
            'clock_out'=>'18:00',
            'break_start'=>['12:00'],
            'break_end'=>['13:00'],
            'reason'=>'',
            'status'=>'approved']);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['reason'=>'備考を記入してください']);
    }
}