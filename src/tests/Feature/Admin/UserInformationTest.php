<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class UserInformationTest extends TestCase
{
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
//管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる
    public function test_admin_can_check_all_users_name_and_email()
    {
        $users = User::factory(3)->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('staff.list'));
        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

//ユーザーの勤怠情報が正しく表示される
    public function test_admin_can_see_users_attendances_correctly()
    {
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $dates = collect(range(1, 7))->map(fn($i) => Carbon::today()->subDays($i));

        $dates->each(fn($date) =>
            Attendance::factory()->forDate($date)->create([
                'user_id' => $user->id,
                'clock_in' => $date->copy()->setTime(9, 0),
                'clock_out' => $date->copy()->setTime(18, 0),
                ])
        );
        $response = $this->actingAs($admin)->get(route('each.staff.attendance',['id'=>$user->id]));
        $response->assertSee($user->name);
        $response->assertSee('9:00');
        $this->assertCount(7, Attendance::all());
    }

//「前月」を押下した時に表示月の前月の情報が表示される
    public function test_admin_can_see_previous_month_index()
    {
        $user = $this->createUser();
        $admin = $this->createAdmin();

        $date= Carbon::now()->subMonth()->startOfMonth()->addDays(4);
        Attendance::create([
            'user_id' => $user->id,
            'work_date' =>$date,
            'clock_in' => $date->copy()->setTime(9, 0),
            'clock_out' => $date->copy()->setTime(18, 0),
        ]);
        $response = $this->actingAs($admin)->get(route('each.staff.attendance',['id'=>$user->id]));
        $response->assertSee(Carbon::now()->format('Y/m')); 
        $response = $this->actingAs($admin)->get('/admin/attendance/staff/' . $user->id . '?month=' . Carbon::now()->subMonth()->format('Y-m'));
        $response->assertSee(Carbon::now()->subMonth()->format('Y/m'));
        $response->assertSeeInOrder([
                $date->format('m月d日'),
                '09:00',
                '18:00',
            ]);
    }
//「翌月」を押下した時に表示月の前月の情報が表示される
    public function test_admin_can_see_next_month_index()
    {
        $user = $this->createUser();
        $admin = $this->createAdmin();

        $date= Carbon::now()->addMonth()->startOfMonth()->addDays(4);
        Attendance::create([
            'user_id' => $user->id,
            'work_date' =>$date,
            'clock_in' => $date->copy()->setTime(9, 0),
            'clock_out' => $date->copy()->setTime(18, 0),
        ]);
        $response = $this->actingAs($admin)->get(route('each.staff.attendance',['id'=>$user->id]));
        $response->assertSee(Carbon::now()->format('Y/m')); 
        $response = $this->actingAs($admin)->get('/admin/attendance/staff/' . $user->id . '?month=' . Carbon::now()->addMonth()->format('Y-m'));
        $response->assertSee(Carbon::now()->addMonth()->format('Y/m'));
        $response->assertSeeInOrder([
                $date->format('m月d日'),
                '09:00',
                '18:00',
            ]);
    }

//「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    public function test_admin_can_see_detail_from_index()
    {
        $user = $this->createUser();
        $admin = $this->createAdmin();

        $attendance=$this->createAttendanceWithBreakTime($user);
        $attendanceId=$attendance->id;
        $response = $this->actingAs($admin)->get(route('admin.attendance.detail',['id'=>$attendanceId]));
        $response->assertSee('9:00');
        $response->assertSee('18:00');
        $response->assertSee(Carbon::today()->format('Y年'));
        $response->assertSee(Carbon::today()->format('n月j日'));
    }
}