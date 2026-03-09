<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Models\User;
use App\Http\Responses\LoginResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function(){
            return view('auth.register');
        });

        Fortify::loginView(function(Request $request){
            if ($request->is('admin/login')) {
                return view('admin.auth.login');
            }
            return view('auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();
            if ($request->is('admin/*') && $user->role !== 'admin') {
                throw ValidationException::withMessages([
                    Fortify::username() => ['一般ユーザーの方は通常のログイン画面からログインしてください。'],
                ]);
            }
            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }
        });
    }
}