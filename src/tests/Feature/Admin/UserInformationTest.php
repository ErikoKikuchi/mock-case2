<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class UserInformationTest extends TestCase
{
    use RefreshDatabase;

//管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる

//ユーザーの勤怠情報が正しく表示される

//「前月」を押下した時に表示月の前月の情報が表示される

//「翌月」を押下した時に表示月の前月の情報が表示される

//「詳細」を押下すると、その日の勤怠詳細画面に遷移する


}