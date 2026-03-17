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
                        <a class="nav-link" href="{{route('attendance.list')}}">勤怠一覧</a>
                        <a class="nav-link" href="{{route('staff.list')}}">スタッフ一覧</a>
                        <a class="nav-link" href="{{route('request.list')}}">申請一覧</a>
                        <div class="logout">
                            <form class="logout__button" action="/admin/logout" method="post">@csrf
                                <button class="logout__button--submit" type="submit">ログアウト</button>
                            </form>
                        </div>
                    @elseif(auth()->user()->role === 'user' && auth()->user()->hasVerifiedEmail())
                        <a class="nav-link" href="{{route('attendance.show')}}">勤怠</a>
                        <a class="nav-link" href="{{route('users.attendance.list')}}">勤怠一覧</a>
                        <a class="nav-link" href="{{route('users.request.list')}}">申請</a>
                        <div class="logout">
                            <form class="logout__button" action="/logout" method="post">@csrf
                                <button class="logout__button--submit" type="submit">ログアウト</button>
                            </form>
                        </div>
                    @endif
                @else
                    <!-- 未ログイン時は何も表示しない -->
                @endauth
            </nav>
        </header>
        <main class="main">
            @yield('content')
        </main>
    </body>
</html>