<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;

class BreakTest extends TestCase
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
    private function breakEnd($user)
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 7, 13, 0, 0));
        $this->actingAs($user)->post('/attendance/store', [
            'action' => '休憩戻',
            'break_end'=>now(),
        ]);
    }
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

//休憩ボタンが正しく機能する
    public function test_break_start_button_push_correctly()
    {
        $user=User::factory()->create([
            'email_verified_at' => now(),
            'role'=>'user',
            ]);
        $this->clockIn($user);
        $response= $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');
        $this->breakStart($user);
        $response= $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩中');
    }

//休憩は一日に何回でもできる
    public function test_user_can_take_break_time_several_times()
    {
        $user=User::factory()->create([
            'email_verified_at' => now(),
            'role'=>'user',
        ]);
        $this->clockIn($user);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
        $this->breakStart($user);
        $this->breakEnd($user);
        $response= $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩入');
    }

//休憩戻ボタンが正しく機能する
    public function test_break_end_button_push_correctly()
    {
        $user=User::factory()->create([
            'email_verified_at' => now(),
            'role'=>'user',
            ]);
        $this->clockIn($user);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
        $this->breakStart($user);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩戻');
        $this->breakEnd($user);
        $response= $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
    }

//休憩戻は一日に何回でもできる
    public function test_user_can_push_break_end_button_several_times()
    {
        $user=User::factory()->create([
                'email_verified_at' => now(),
                'role'=>'user',
                ]);
        $this->clockIn($user);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
        $this->breakStart($user);
        $this->breakEnd($user);

        Carbon::setTestNow(Carbon::create(2026, 4, 7, 15, 0, 0));
        $this->actingAs($user)->post('/attendance/store', [
            'action' => '休憩入',
            'break_start'=>now(),
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩戻');
    }

//休憩時刻が勤怠一覧画面で確認できる
    public function test_break_time_can_see_index()
    {
        $user=User::factory()->create([
            'email_verified_at' => now(),
            'role'=>'user',
            ]);
        $this->clockIn($user);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
        $this->breakStart($user);
        $this->breakEnd($user);
        $response= $this->actingAs($user)->get('/attendance/list');
        $response->assertSee('2026/04');
        $response->assertSee('04/07');
        $response->assertSee('1:00');
    }

}
