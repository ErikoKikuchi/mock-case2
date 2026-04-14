<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use App\Models\RequestItem;
use Carbon\Carbon;

class ApproveTest extends TestCase
{
    use RefreshDatabase;
    private function createUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);
    }
    private function createAFewUsers(): Collection
    {
        return User::factory(3)->create([
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
    private function createRequestAttendanceWithBreakTime(User $user, string $status = 'pending')
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
        $attendanceRequest=AttendanceRequest::factory()->for($attendance)->create([
            'user_id'  => $user->id,
            'attendance_id'=>$attendance->id,
            'reason'=>'テスト',
            'status'=>$status,
            'requested_by' => $user->id,
        ]);
        RequestItem::create([
            'request_id'=> $attendanceRequest->id,
            'attendance_id'=>$attendance->id,
            'column_name'=>'clock_in',
            'before_value'=>$attendance->clock_in,
            'after_value'=>$date->copy()->setTime(10,0),
        ]);
        return $attendanceRequest;
    }
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

//承認待ちの修正申請が全て表示されている
    public function test_admin_can_see_all_pending_request()
    {
        $users = $this->createAFewUsers();
        $attendanceRequests=$users->map(fn($user) => $this->createRequestAttendanceWithBreakTime($user));
        $admin = $this->createAdmin();
        $response=$this->actingAs($admin)->get(route('request.list'));
        foreach ($attendanceRequests as $request) {
            $response->assertSee($request->user->name);
            $response->assertSee($request->reason);
        }
    }

//承認済みの修正申請が全て表示されている
    public function test_admin_can_see_all_approved_request()
    {
        $users = $this->createAFewUsers();
        $attendanceRequests=$users->map(fn($user) => $this->createRequestAttendanceWithBreakTime($user,'approved'));
        $admin = $this->createAdmin();
        $response=$this->actingAs($admin)->get(route('request.list',['tab'=>'approved']));
        foreach ($attendanceRequests as $request) {
            $response->assertSee($request->user->name);
            $response->assertSee($request->reason);
        }
    }

//修正申請の詳細内容が正しく表示されている
    public function test_pending_request_detail_can_see_correctly_by_admin()
    {
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $attendanceRequest=$this->createRequestAttendanceWithBreakTime($user);
        $requestId=$attendanceRequest->id;
        $response=$this->actingAs($admin)->get(route('request.approve.detail',['attendance_correct_request_id'=>$requestId]));
        $response->assertSee($user->name);
        $response->assertSee('10:00');
    }

//修正申請の承認処理が正しく行われる
    public function test_admin_can_approve_request_correctly()
    {
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $attendanceRequest=$this->createRequestAttendanceWithBreakTime($user);
        $requestId=$attendanceRequest->id;
        $response=$this->actingAs($admin)->get(route('each.staff.attendance',['id'=>$user->id]));
        $response->assertSee($user->name);
        $response->assertSee('9:00');
        $response=$this->actingAs($admin)->post(route('update.attendance'),[
            'attendance_request_id' => $requestId,
            'attendance_id'         => $attendanceRequest->attendance_id,
            'status' => 'approved',
        ]);
        $response=$this->actingAs($admin)->get(route('each.staff.attendance',['id'=>$user->id]));
        $response->assertSee($user->name);
        $response->assertSee('10:00');
    }
}