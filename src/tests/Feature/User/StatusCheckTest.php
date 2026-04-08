<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;

class StatusCheckTest extends TestCase
{
    use RefreshDatabase;

    private function clockIn($user)
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 7, 9, 0, 0));
        $this->actingAs($user)->post('/attendance/store', [
            'action' => '出勤',
            'work_date' => Carbon::today(),
            'clock_in' => now(),
        ]);
    }
    private function clockOut($user)
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 7, 18, 0, 0));
        $this->actingAs($user)->post('/attendance/store', [
            'action' => '退勤',
            'clock_out' => now(),
        ]);
    }
    private function breakStart($user)
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 7, 12, 0, 0));
        $this->actingAs($user)->post('/attendance/store', [
            'action' => '休憩入',
            'break_start'=>now(),
        ]);
    }
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

//勤務外の場合、勤怠ステータスが正しく表示される
    public function test_correctly_display_status_before_clock_in()
        {
            $user=User::factory()->create([
                'email_verified_at' => now(),
                'role'=>'user',
                ]);
            $response = $this->actingAs($user)->get('/attendance');
            $response->assertSee('勤務外');
            $response->assertSee('出勤');
        }

//出勤中の場合、勤怠ステータスが正しく表示される
    public function test_correctly_display_status_after_clock_in()
        {
            $user=User::factory()->create([
                'email_verified_at' => now(),
                'role'=>'user',
                ]);
            $this->clockIn($user);
            $response= $this->actingAs($user)->get('/attendance');
            $response->assertSee('出勤中');
        }

//休憩中の場合、勤怠ステータスが正しく表示される
    public function test_correctly_display_status_during_break_time()
        {
            $user=User::factory()->create([
                'email_verified_at' => now(),
                'role'=>'user',
                ]);
            $this->clockIn($user);
            $this->breakStart($user);
            $response= $this->actingAs($user)->get('/attendance');
            $response->assertSee('休憩中');
        }

//退勤済の場合、勤怠ステータスが正しく表示される
    public function test_correctly_display_status_after_clock_out()
        {
            $user=User::factory()->create([
                'email_verified_at' => now(),
                'role'=>'user',
                ]);
            $this->clockIn($user);
            $this->clockOut($user);
            $response= $this->actingAs($user)->get('/attendance');
            $response->assertSee('退勤済');
        }
}