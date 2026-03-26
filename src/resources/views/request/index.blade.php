@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/users/request-index.js')
    @endif
@endsection

@section('content')
<div class="users-request-index">
    @if(session('message'))
        <div class="message-box">
            <p class="message">{{session('message')}}</p>
        </div>
    @endif
    <div class="request-index__title">| 申請一覧</div>
    <div class="tab-group">
        <div class="index-tab {{ request('tab','pending')==='pending'?'active':''}}">
            <a class="index__link "href="{{route('users.request.list',['tab'=>'pending'])}}">承認待ち</a>
        </div>
        <div class="index-tab {{ request('tab')==='approved'?'active':''}}">
            <a class="index__link "href="{{route('users.request.list',['tab'=>'approved'])}}">承認済み</a>
        </div>
    </div>
</div>
@endsection