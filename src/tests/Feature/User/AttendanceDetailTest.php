<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

//勤怠詳細画面の「名前」がログインユーザーの氏名になっている
    public function test_user_can_see_name_on_detail_display()
    {
        $user=User::factory()->create([
            'name'=>'テストユーザー',
            'email_verified_at' => now(),
            'role'=>'user',
            ]);
        $response = $this->actingAs($user)->get('/attendance/detail?date=' . Carbon::now()->format('Y-m-d'));
        $response->assertSee('テストユーザー');
    }


//勤怠詳細画面の「日付」が選択した日付になっている


//「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している


//「休憩」にて記されている時間がログインユーザーの打刻と一致している



}
