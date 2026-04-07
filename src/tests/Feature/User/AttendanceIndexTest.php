<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AttendanceIndexTest extends TestCase
{
    use RefreshDatabase;

//自分が行った勤怠情報が全て表示されている


//勤怠一覧画面に遷移した際に現在の月が表示される


//「前月」を押下した時に表示月の前月の情報が表示される


//「翌月」を押下した時に表示月の前月の情報が表示される


//「詳細」を押下すると、その日の勤怠詳細画面に遷移する



}