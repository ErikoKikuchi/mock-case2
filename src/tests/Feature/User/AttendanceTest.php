<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;

class AttendanceTest extends TestCase
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
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

//現在の日時情報がUIと同じ形式で出力されている
    public function test_date_information_can_see_same_with_UI()
    {
        $user=User::factory()->create([
            'email_verified_at' => now(),
            'role'=>'user',
            ]);

        Carbon::setTestNow(Carbon::create(2026, 4, 7, 9, 0, 0));

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('2026年4月7日(火)');
        $response->assertSee('09:00');
        Carbon::setTestNow();
    }

//出勤ボタンが正しく機能する
    public function test_clock_in_button_work_correctly()
    {
        $user=User::factory()->create([
            'email_verified_at' => now(),
            'role'=>'user',
            ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('勤務外');
        $response->assertSee('出勤');
        $this->clockIn($user);
        $response= $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
    }

//出勤は一日一回のみできる
    public function test_clock_in_button_can_use_once()
    {
        $user=User::factory()->create([
            'email_verified_at' => now(),
            'role'=>'user',
            ]);
        $this->clockIn($user);
        $this->clockOut($user);
        $response= $this->actingAs($user)->get('/attendance');
        $response->assertSee('退勤済');
        $response->assertDontSee('出勤');
    }

//出勤時刻が勤怠一覧画面で確認できる
    public function test_clock_in_time_can_see_index()
    {
        $user=User::factory()->create([
            'email_verified_at' => now(),
            'role'=>'user',
            ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('勤務外');
        $response->assertSee('出勤');
        $this->clockIn($user);
        $response= $this->actingAs($user)->get('/attendance/list');
        $response->assertSee('2026/04');
        $response->assertSee('04/07');
        $response->assertSee('09:00');
    }

//退勤ボタンが正しく機能する
    public function test_clock_out_button_work_correctly()
    {
        $user=User::factory()->create([
            'email_verified_at' => now(),
            'role'=>'user',
            ]);
        $this->clockIn($user);
        $response= $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('退勤');
        $this->clockOut($user);
        $response= $this->actingAs($user)->get('/attendance');
        $response->assertSee('退勤済');
    }

//退勤時刻が勤怠一覧画面で確認できる
    public function test_clock_out_time_can_see_index()
    {
        $user=User::factory()->create([
            'email_verified_at' => now(),
            'role'=>'user',
            ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('勤務外');
        $response->assertSee('出勤');
        $this->clockIn($user);
        $this->clockOut($user);
        $response= $this->actingAs($user)->get('/attendance/list');
        $response->assertSee('2026/04');
        $response->assertSee('04/07');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }
}