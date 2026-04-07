<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

//勤怠詳細画面に表示されるデータが選択したものになっている


//出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される


//休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される


//休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される

//備考欄が未入力の場合のエラーメッセージが表示される

}