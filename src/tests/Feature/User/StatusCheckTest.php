<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class StatusCheckTest extends TestCase
{
    use RefreshDatabase;

//勤務外の場合、勤怠ステータスが正しく表示される


//出勤中の場合、勤怠ステータスが正しく表示される


//休憩中の場合、勤怠ステータスが正しく表示される


//退勤済の場合、勤怠ステータスが正しく表示される


}