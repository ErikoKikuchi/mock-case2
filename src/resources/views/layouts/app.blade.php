<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>COACHTECH</title>
        @if(!app()->environment('testing') && !config('app.vite_disabled'))
            @vite('resources/js/app.js')
            @yield('css')
        @endif
    </head>
    <body>
        <header class="header">
            <div class="header__inner">
                <img class="header__logo" src="{{asset('images/COACHTECHヘッダーロゴ .png')}}" type="image" name="logo">
            </div>
            <nav class = "nav">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="">勤怠一覧</a>
                        <a href="">スタッフ一覧</a>
                        <a href="">申請一覧</a>
                        <a href="">ログアウト</a>
                    @else
                        <a href="">勤怠</a>
                        <a href="">勤怠一覧</a>
                        <a href="">申請</a>
                        <a href="">ログアウト</a>
                    @endif
                    @else
                @endauth
            </nav>
        </header>
        <main class="main">
            @yield('content')
        </main>
    </body>
</html>