<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\RequestItem;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceChangeRequestTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);
    }
    private function createAttendanceWithBreakTime(User $user)
    {
        $date = Carbon::today()->subDays(1);

        $attendance = Attendance::factory()->forDate($date)->create([
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

    private function createAttendanceChangeRequest(User $user){
        $dates = collect(range(1, 7))->map(fn($i) => Carbon::today()->subDays($i));

        return $dates->map(function($date) use ($user) {
            $attendance = Attendance::factory()->forDate($date)->create([
                'user_id' => $user->id,
                'clock_in' => $date->copy()->setTime(9, 0),
                'clock_out' => $date->copy()->setTime(18, 0),
            ]);
            $attendanceRequest=AttendanceRequest::factory()->create([
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'reason'=>'テスト',
                'status'=>'pending',
                ]);
            RequestItem::factory()->create([
                'request_id'=>$attendanceRequest->id,
                'attendance_id'=> $attendance->id,
                'column_name'=>'clock_in',
                'before_value'=>'09:00',
                'after_value'=>'10:00',
            ]);
            return $attendance;
            });
    }

    private function createApprovedAttendanceChangeRequest(User $user, User $adminUser): void
    {
        $dates = collect(range(1, 7))->map(fn($i) => Carbon::today()->subDays($i));

        $dates->each(function($date) use ($user, $adminUser) {
            $attendance = Attendance::factory()->forDate($date)->create([
                'user_id'   => $user->id,
                'clock_in'  => $date->copy()->setTime(9, 0),
                'clock_out' => $date->copy()->setTime(18, 0),
            ]);
            AttendanceRequest::factory()->create([
                'attendance_id' => $attendance->id,
                'user_id'       => $user->id,
                'reason'        => 'テスト',
                'status'        => 'approved',
                'approved_by'   => $adminUser->id,
                'approved_at'   => Carbon::now(),
            ]);
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }


//出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_error_message_is_displayed_if_clockIn_is_after_clockOut()
    {
        $user = $this->createUser();
        $attendance=$this->createAttendanceWithBreakTime($user);
        $attendanceId = $attendance->id;
        $response = $this->actingAs($user)->get(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSee('9:00');
        $response=$this->actingAs($user)->post(route('users.attendance.request'),[
            'attendance_id' => $attendanceId,
            'clock_in'=>'19:00',
            'clock_out'=>'18:00',
            'reason'=>'テスト',
            'status'=>'pending']);
        $response->assertRedirect(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSessionHasErrors(['clock_out'=>'出勤時間が不適切な値です']);
    }
//休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_error_message_is_displayed_if_breakStart_is_after_clockOut()
    {
        $user = $this->createUser();
        $attendance=$this->createAttendanceWithBreakTime($user);
        $attendanceId = $attendance->id;
        $response = $this->actingAs($user)->get(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSee('9:00');
        $response=$this->actingAs($user)->post(route('users.attendance.request'),[
            'attendance_id' => $attendanceId,
            'clock_in'=>'09:00',
            'clock_out'=>'18:00',
            'break_start'=>['19:00'],
            'break_end'=>['13:00'],
            'reason'=>'テスト',
            'status'=>'pending']);
        $response->assertRedirect(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSessionHasErrors(['break_start.0'=>'休憩時間が不適切な値です']);
    }
//休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_error_message_is_displayed_if_breakEnd_is_after_clockOut()
    {
        $user = $this->createUser();
        $attendance=$this->createAttendanceWithBreakTime($user);
        $attendanceId = $attendance->id;
        $response = $this->actingAs($user)->get(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSee('9:00');
        $response=$this->actingAs($user)->post(route('users.attendance.request'),[
            'attendance_id' => $attendanceId,
            'clock_in'=>'09:00',
            'clock_out'=>'18:00',
            'break_start'=>['12:00'],
            'break_end'=>['19:00'],
            'reason'=>'テスト',
            'status'=>'pending']);
        $response->assertRedirect(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSessionHasErrors(['break_end.0'=>'休憩時間もしくは退勤時間が不適切な値です']);
    }
//備考欄が未入力の場合のエラーメッセージが表示される
    public function test_error_message_is_displayed_if_reason_is_null()
    {
        $user = $this->createUser();
        $attendance=$this->createAttendanceWithBreakTime($user);
        $attendanceId = $attendance->id;
        $response = $this->actingAs($user)->get(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSee('9:00');
        $response=$this->actingAs($user)->post(route('users.attendance.request'),[
            'attendance_id' => $attendanceId,
            'clock_in'=>'09:00',
            'clock_out'=>'18:00',
            'break_start'=>['12:00'],
            'break_end'=>['13:00'],
            'reason'=>'',
            'status'=>'pending']);
        $response->assertRedirect(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSessionHasErrors(['reason'=>'備考を記入してください']);
    }
//修正申請処理が実行される
    public function test_attendance_change_request_can_do_correctly()
    {
        $user = $this->createUser();
        $adminUser=User::factory()->create([
            'role'=>'admin',
            ]);

        $attendance=$this->createAttendanceWithBreakTime($user);
        $attendanceId = $attendance->id;
        $response = $this->actingAs($user)->get(route('users.attendance.detail',['id'=>$attendanceId]));
        $response->assertSee('9:00');

        $response=$this->actingAs($user)->post(route('users.attendance.request'),[
            'attendance_id' => $attendanceId,
            'clock_in'=>'09:00',
            'clock_out'=>'18:00',
            'break_start'=>['13:00'],
            'break_end'=>['14:00'],
            'reason'=>'テスト',
            'status'=>'pending']);
        $attendanceRequest = AttendanceRequest::where('attendance_id', $attendanceId)
            ->latest()
            ->first();
        $requestId = $attendanceRequest->id;

        $response=$this->actingAs($adminUser)->get(route('request.list'));
        $response->assertSee('テスト');
        $response->assertSee(Carbon::today()->subDays(1)->format('n月j日'));
        $response=$this->actingAs($adminUser)->get(route('request.approve.detail',['attendance_correct_request_id'=>$requestId]));

        $response->assertSee('テスト');
        $response->assertSee('14:00');
        $response->assertSee(Carbon::today()->subDays(1)->format('n月j日'));
    }
//「承認待ち」にログインユーザーが行った申請が全て表示されていること
    public function test_user_can_see_attendance_change_request()
    {
        $user = $this->createUser();

        $this->createAttendanceChangeRequest($user);

        $response = $this->actingAs($user)->get(route('users.request.list'));
        $response->assertSee('テスト');
        $response->assertSee(Carbon::today()->subDays(1)->format('n月j日'));
        $response->assertSee(Carbon::today()->subDays(3)->format('n月j日'));
        $response->assertSee(Carbon::today()->subDays(5)->format('n月j日'));
        $this->assertDatabaseCount('attendances', 7);
    }

//「承認済み」に管理者が承認した修正申請が全て表示されている
    public function test_user_can_see_approved_attendance_change_request()
    {
        $user = $this->createUser();
        $adminUser=User::factory()->create([
            'role'=>'admin',
            ]);

        $this->createApprovedAttendanceChangeRequest($user,$adminUser);

        $response = $this->actingAs($user)->get(route('users.request.list',['tab'=>'approved']));
        $response->assertSee('テスト');
        $response->assertSee(Carbon::today()->subDays(1)->format('n月j日'));
        $response->assertSee(Carbon::today()->subDays(3)->format('n月j日'));
        $response->assertSee(Carbon::today()->subDays(5)->format('n月j日'));
        $this->assertDatabaseCount('attendances', 7);
    }

//各申請の「詳細」を押下すると勤怠詳細画面に遷移する
        public function test_user_can_move_detail_from_request_index()
    {
        $user = $this->createUser();

        $attendance=$this->createAttendanceChangeRequest($user);
        $attendanceId = $attendance->first()->id;

        $response = $this->actingAs($user)->get(route('users.request.list'));
        $response->assertOk();
        $response->assertSee('詳細');
        $response = $this->actingAs($user)->get(route('users.attendance.detail', ['id' => $attendanceId]));
        $response->assertSee('修正');
    }
}
