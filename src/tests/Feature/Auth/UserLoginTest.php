<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

//メールアドレスが未入力の場合、バリデーションメッセージが表示される
    public function test_login_requires_email()
    {
        $data =[
            'password' => 'password',
        ];
        $response = $this->from('/login')->post('/login',$data);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email'=>'メールアドレスを入力してください',
        ]);
    }

//パスワードが未入力の場合、バリデーションメッセージが表示される
    public function test_login_requires_password()
    {
        $data =[
            'email' => 'testuser@example.com',
        ];
        $response = $this->from('/login')->post('/login',$data);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'password'=>'パスワードを入力してください',
            ]);
    }

//登録内容と一致しない場合、バリデーションメッセージが表示される
    public function test_user_did_not_register()
    {
        $user = [
            'email' => 'user2@example.com',
            'password' => Hash::make('password123'),
        ];
        $response = $this->from('/login')->post('/login', $user);
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
        $response->assertRedirect('/login');
    }

}