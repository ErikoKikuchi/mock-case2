@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/users/verify-email.js')
    @endif
@endsection

@section('content')
    <div class="container">
        <div class="verify-email__form">
            <div class="verify-email__message">
                <p class="verify-email__main-sentence">登録していただいたメールアドレスに認証メールを送付しました。</p>
                <p class="verify-email__main-sentence">メール内認証を完了してください。</p>
            </div>
            <form class="verification-form" method="GET" action="{{ route('verification.open') }}">
                <button class="verification-form__submit" type="submit">認証はこちらから</button>
            </form>
            <form class="verification-resend" method="POST" action="{{ route('verification.send') }}" >@csrf
                <button class="verification-form__resend" type="submit">認証メールを再送する</button>
            </form>
        </div>
    </div>
@endsection