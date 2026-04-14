@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/admin/login.js')
    @endif
@endsection

@section('content')
<div class="container">
    <div class="login-form">
        <div class="login-form__title">
            <h1 class="form__title">管理者ログイン</h1>
        </div>
        <form class="login-form__inner" action="/admin/login" method="post">@csrf
            <div class="login-form__email">
                <div class="login-form__label">
                    <label class="login-form__label-title" for="email">メールアドレス</label>
                </div>
                <div class="login-form__input">
                    <input class="login-form__input-box" type="text" id="email" name="email" value="{{old('email')}}">
                </div>
                <div class="error">
                    @foreach($errors->get('email') as $message)
                        <p class="error-message">{{$message}}</p>
                    @endforeach
                </div>
            </div>
            <div class="login-form__password">
                <div class="login-form__label">
                    <label class="login-form__label-title" for="password">パスワード</label>
                </div>
                <div class="login-form__input">
                    <input class="login-form__input-box" type="password" id="password" name="password">
                </div>
                <div class="error">
                    @error('password')
                        <p class="error-message">{{$message}}</p>
                    @enderror
                </div>
            </div>
            <div class="login-form__button">
                <button class="login__button" type="submit">管理者ログインする</button>
            </div>
        </form>
    </div>
</div>
@endsection