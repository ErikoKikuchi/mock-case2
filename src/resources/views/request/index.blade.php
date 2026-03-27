@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/users/request-index.js')
    @endif
@endsection

@section('content')
<div class="content">
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
        <div class="request-index__content">
            <table class="request-index__table">
                <tr class ="request-index__row">
                    <th class="request-index__header">状態</th>
                    <th class="request-index__header">名前</th>
                    <th class="request-index__header">対象日時</th>
                    <th class="request-index__header">申請理由</th>
                    <th class="request-index__header">申請日時</th>
                    <th class="request-index__header">詳細</th>
                </tr>
                @foreach($requestItems as $item)
                    <tr class ="request-index__row">
                        <td class="request-index__description">
                            <div class="request-status">
                                {{$item->statusLabel}}
                            </div>
                        </td>
                        <td class="request-index__description">
                            <div class="request-user">
                                {{$user->name}}
                            </div>
                        </td>
                        <td class="request-index__description">
                            <div class="request-date">
                                {{$item->attendance->work_date->isoFormat('M月D日')}}
                            </div>
                        </td>
                        <td class="request-index__description">
                            <div class="request-reason">
                                {{$item->reason}}
                            </div>
                        </td>
                        <td class="request-index__description">
                            <div>
                                {{$item->created_at->isoFormat('M月D日')}}
                            </div>
                        </td>
                        <td class="request-index__description">
                            <div>
                                <a class="request-detail__button" href="{{route('users.attendance.detail',['id'=>$item->attendance->id])}}">詳細</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
            </table>
        </div>
    </div>
</div>
@endsection