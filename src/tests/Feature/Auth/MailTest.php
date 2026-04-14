<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

class MailTest extends TestCase
{
    use RefreshDatabase;

//会員登録後、認証メールが送信される
    public function test_after_register_email_sent()
    {
        Notification::fake();

        $data = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $this
        ->from('/register')
        ->post('/register', $data);
        $user = User::where('email', 'testuser@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

//メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
    public function test_when_pushed_verified_button_move_email_site()
    {
        config('app.mail_verify_provider_url');
        $data = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $response = $this
        ->from('/register')
        ->post('/register', $data);
        $response->assertRedirect('/email/verify');
        $next = $this->get('/redirect');
        $next->assertRedirect(config('app.mail_verify_provider_url'));
    }

//メール認証サイトのメール認証を完了すると、勤怠登録画面に遷移する
    public function test_finish_verify_move_to_attendance_register_form()
    {
        Event::fake([Verified::class]);

        $data = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $answer=$this->from('/register')
        ->post('/register', $data);
        $user = User::where('email', 'testuser@example.com')->firstOrFail();

        $answer->assertRedirectToRoute('verification.notice');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());

        // 認証用URLをテスト内で生成（署名付き）
        $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);
        $response->assertRedirect(route('attendance.show'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

}