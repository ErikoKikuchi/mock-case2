@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/users/register.js')
    @endif
@endsection

@section('content')
<div class="container">
    <div class ="register-form">
        <h1 class="register-form__title">会員登録</h1>
        <div class="form__inner">
            <form class="register-form__inner" action="/register" method="post">@csrf
                <div class="register-form__name">
                    <div class="register-form__label">
                        <label class="register-form__label-title" for="name">名前</label>
                    </div>
                    <div class="register-form__input">
                        <input class="register-form__input-box" type="text" id="name" name="name" value="{{old('name')}}">
                    </div>
                    <div class="error">
                        @foreach($errors->get('name') as $message)
                            <p class="error-message">{{$message}}</p>
                        @endforeach
                    </div>
                </div>
                <div class="register-form__email">
                    <div class="register-form__label">
                        <label class="register-form__label-title" for="email">メールアドレス</label>
                    </div>
                    <div class="register-form__input">
                        <input class="register-form__input-box" type="text" id="email" name="email" value="{{old('email')}}">
                    </div>
                    <div class="error">
                        @foreach($errors->get('email') as $message)
                            <p class="error-message">{{$message}}</p>
                        @endforeach
                    </div>
                </div>
                <div class="register-form__password">
                    <div class="register-form__label">
                        <label class="register-form__label-title" for="password">パスワード</label>
                    </div>
                    <div class="register-form__input">
                        <input class="register-form__input-box" type="password" id="password" name="password" ">
                    </div>
                    <div class="error">
                        @error('password')
                            <p class="error-message">{{$message}}</p>
                        @enderror
                    </div>
                </div>
                <div class="register-form__password_confirmation">
                    <div class="register-form__label">
                        <label class="register-form__label-title" for="password_confirmation">パスワード確認</label>
                    </div>
                    <div class="register-form__input">
                        <input class="register-form__input-box" type="password" id="password_confirmation" name="password_confirmation" >
                    </div>
                    <div class="error">
                        @error('password_confirmation')
                            <p class="error-message">{{$message}}</p>
                        @enderror
                    </div>
                </div>
                <div class="register-form__button">
                    <button class="register__button" type="submit">登録する</button>
                </div>
            </form>
        </div>
        <div class="move-to-login">
            <a class="login-link" href="/login">ログインはこちら</a>
        </div>
    </div>
</div>
@endsection