<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AttendanceIndexTest extends TestCase
{
    use RefreshDatabase;

//その日になされた全ユーザーの勤怠情報が正確に確認できる


//遷移した際に現在の日付が表示される


//「前日」を押下した時に前の日の勤怠情報が表示される

//「翌日」を押下した時に次の日の勤怠情報が表示される


}