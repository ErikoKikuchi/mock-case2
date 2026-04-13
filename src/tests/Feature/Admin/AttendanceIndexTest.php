<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceIndexTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): Collection
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

//その日になされた全ユーザーの勤怠情報が正確に確認できる
    public function test_admin_can_see_all_attendance_information()
    {
        $users = $this->createUser();
        $users->each(fn($user) => $this->createAttendanceWithBreakTime($user));
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('attendance.list'));
        foreach ($users as $user) {
            $response->assertSee($user->name);
        }
        $this->assertCount(3, Attendance::all());
        $response->assertSee('09:00');
    }

//遷移した際に現在の日付が表示される
    public function test_admin_can_see_todays_index()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('attendance.list'));
        $response->assertSee(Carbon::now()->format('Y/m/d'));
    }

//「前日」を押下した時に前の日の勤怠情報が表示される
    public function test_admin_can_see_previous_day_index()
    {
        $users = $this->createUser();
        $admin = $this->createAdmin();

        $date= Carbon::now()->subDay();
        $users->each(fn($user) => Attendance::create([
            'user_id' => $user->id,
            'work_date' =>$date,
            'clock_in' => $date->copy()->setTime(9, 0),
            'clock_out' => $date->copy()->setTime(18, 0),
        ]));
        $response = $this->actingAs($admin)->get(route('attendance.list'));
        $response->assertSee(Carbon::now()->format('Y/m/d')); 
        $response = $this->actingAs($admin)->get('/admin/attendance/list?day=' . Carbon::now()->subDay()->format('Y-m-d'));
        $response->assertSee(Carbon::now()->subDay()->format('Y/m/d'));
        $response->assertSee('09:00');
    }
//「翌日」を押下した時に次の日の勤怠情報が表示される
    public function test_admin_can_see_next_day_index()
    {
        $users = $this->createUser();
        $admin = $this->createAdmin();

        $date= Carbon::now()->addDay();
        $users->each(fn($user) => Attendance::create([
            'user_id' => $user->id,
            'work_date' =>$date,
            'clock_in' => $date->copy()->setTime(9, 0),
            'clock_out' => $date->copy()->setTime(18, 0),
        ]));
        $response = $this->actingAs($admin)->get(route('attendance.list'));
        $response->assertSee(Carbon::now()->format('Y/m/d')); 
        $response = $this->actingAs($admin)->get('/admin/attendance/list?day=' . Carbon::now()->addDay()->format('Y-m-d'));
        $response->assertSee(Carbon::now()->addDay()->format('Y/m/d'));
        $response->assertSee('09:00');
    }
}