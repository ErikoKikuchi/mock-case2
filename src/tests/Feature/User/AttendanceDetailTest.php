<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

//勤怠詳細画面の「名前」がログインユーザーの氏名になっている


//勤怠詳細画面の「日付」が選択した日付になっている


//「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している


//「休憩」にて記されている時間がログインユーザーの打刻と一致している



}