<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

//名前が未入力の場合、バリデーションメッセージが表示される
    public function test_registration_requires_name()
    {
        $data =[
            'email' => 'testuser@example.com',
            'password' =>'password',
            'password_confirmation'=> 'password'
        ];
        $response = $this->from('/register')->post('/register',$data);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'name'=>'お名前を入力してください',
        ]);
    }

//メールアドレスが未入力の場合、バリデーションメッセージが表示される
    public function test_registration_requires_email()
    {
        $data =[
            'name' => 'テストユーザー',
            'password' =>'password',
            'password_confirmation'=> 'password'
        ];
        $response = $this->from('/register')->post('/register',$data);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'email'=>'メールアドレスを入力してください'
        ]);
    }

//パスワードが8文字未満の場合、バリデーションメッセージが表示される
    public function test_registration_requires_password_at_least_8_characters()
    {
        $data =[
            'name' => 'テストユーザー',
            'email' => 'testuser@example.com',
            'password' =>'pass',
            'password_confirmation'=> 'pass'
        ];
        $response = $this->from('/register')->post('/register',$data);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'password'=>'パスワードは８文字以上で入力してください'
        ]);
    }

//パスワードが一致しない場合、バリデーションメッセージが表示される
    public function test_registration_fails_if_password_confirmation_does_not_match()
    {
        $data =[
            'name' => 'テストユーザー',
            'email' => 'testuser@example.com',
            'password' =>'password',
            'password_confirmation'=> 'password123'
        ];
        $response = $this->from('/register')->post('/register',$data);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'password_confirmation'=>'パスワードと一致しません'
        ]);
    }

//パスワードが未入力の場合、バリデーションメッセージが表示される
    public function test_registration_requires_password()
    {
        $data =[
            'name' => 'テストユーザー',
            'email' => 'testuser@example.com',
            'password_confirmation'=> 'password'
        ];
        $response = $this->from('/register')->post('/register',$data);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'password'=>'パスワードを入力してください'
        ]);
    }

//フォームに内容が入力されていた場合、データが正常に保存される。応用要件（メール認証）実装したため、登録後のプロフィール遷移は別テストで担保。
    public function test_user_can_register_with_valid_credentials()
    {
        $data = [
            'name' => 'テストユーザー',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $this
        ->from('/register')
        ->post('/register', $data);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

}