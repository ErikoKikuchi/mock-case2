<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceIndexTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);
    }
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

//自分が行った勤怠情報が全て表示されている
    public function test_user_can_see_all_attendance_information()
    {
        $user = $this->createUser();
        $dates = collect(range(1, 7))->map(fn($i) => Carbon::today()->subDays($i));

        $dates->each(fn($date) =>
            Attendance::factory()->forDate($date)->create([
                'user_id' => $user->id,
                'clock_in' => $date->copy()->setTime(9, 0),
                'clock_out' => $date->copy()->setTime(18, 0),
                ])
        );

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertStatus(200);

        $dates->each(function($date) use ($response) {
            $response->assertSeeInOrder([
                $date->format('m/d'),
                '09:00',
                '18:00',
            ]);
        });
        $this->assertDatabaseCount('attendances', 7);
    }

//勤怠一覧画面に遷移した際に現在の月が表示される
    public function test_user_can_see_this_month_index()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee(Carbon::now()->format('Y/m'));
    }

//「前月」を押下した時に表示月の前月の情報が表示される
    public function test_user_can_see_previous_month_index()
    {
        $user = $this->createUser();

        $date= Carbon::now()->subMonth()->startOfMonth()->addDays(4);
        Attendance::create([
            'user_id' => $user->id,
            'work_date' =>$date,
            'clock_in' => $date->copy()->setTime(9, 0),
            'clock_out' => $date->copy()->setTime(18, 0),
        ]);
        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee(Carbon::now()->format('Y/m')); 
        $response = $this->actingAs($user)->get('/attendance/list?month=' . Carbon::now()->subMonth()->format('Y-m'));
        $response->assertSee(Carbon::now()->subMonth()->format('Y/m'));
        $response->assertSeeInOrder([
                $date->format('m/d'),
                '09:00',
                '18:00',
            ]);
    }

//「翌月」を押下した時に表示月の前月の情報が表示される
    public function test_user_can_see_next_month_index()
    {
        $user = $this->createUser();

        $date= Carbon::now()->addMonth()->startOfMonth()->addDays(4);
        Attendance::create([
            'user_id' => $user->id,
            'work_date' =>$date,
            'clock_in' => $date->copy()->setTime(9, 0),
            'clock_out' => $date->copy()->setTime(18, 0),
        ]);
        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee(Carbon::now()->format('Y/m')); 
        $response = $this->actingAs($user)->get('/attendance/list?month=' . Carbon::now()->addMonth()->format('Y-m'));
        $response->assertSee(Carbon::now()->addMonth()->format('Y/m'));
        $response->assertSeeInOrder([
                $date->format('m/d'),
                '09:00',
                '18:00',
            ]);
    }

//「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    public function test_user_can_see_detail_of_selected_day()
    {
        $user = $this->createUser();
        $date = Carbon::today()->subDays(1);

        $attendance = Attendance::factory()->forDate($date)->create([
            'user_id'  => $user->id,
            'clock_in' => $date->copy()->setTime(9, 0),
            'clock_out' => $date->copy()->setTime(18, 0),
        ]);
        $attendanceId = $attendance->id;
        $response = $this->actingAs($user)->get(route('users.attendance.detail',[ 'id'=>$attendanceId]));
        $response->assertSee('修正');
        $response->assertSee(Carbon::now()->subDay()->format('n月j日'));
    }
}