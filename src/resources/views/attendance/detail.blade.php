@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/users/attendance-detail.js')
    @endif
@endsection

@section('content')
<div class="attendance-detail">
    <div class="attendance-detail__title">| 勤怠詳細</div>
    @if($attendanceRequest && $attendanceRequest->statusLabel === '申請中')
        <table class="attendance-detail__table">
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">名前</th>
                <td class="attendance-detail__description">{{$user->name}}</td>
            </tr>
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">日付</th>
                <td class="attendance-detail__description">{{$attendance->work_date->format('Y年m月d日')}}</td>
            </tr>
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">出勤・退勤</th>
                <td class="attendance-detail__description">{{$attendance->clock_in?->format('H:i')}}</td>
            </tr>
            @foreach($attendance->breakTimes as $breakTime)
                <tr class="attendance-detail__row">
                    <th class="attendance-detail__header">休憩</th>
                    <td class="attendance-detail__description">{{$breakTime->break_start?->format('H:i')}}〜{{$breakTime->break_end?->format('H:i')}}</td>
                </tr>
            @endforeach
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">備考</th>
                <td class="attendance-detail__description">{{$attendanceRequest?->reason}}</td>
            </tr>
        </table>
        <div class="request-alert">
            <p class="alert-message">*承認待ちのため修正はできません</p>
        </div>
    @else
        <form action="" method="post">
            <table class="attendance-detail__table">
                <tr class="attendance-detail__row">
                    <th class="attendance-detail__header">名前</th>
                    <td class="attendance-detail__description">{{$user->name}}
                    </td>
                </tr>
                <tr class="attendance-detail__row">
                    <th class="attendance-detail__header">日付</th>
                    <td class="attendance-detail__description">{{$attendance->work_date->format('Y年')}}</td>
                    <td class="attendance-detail__description">{{$attendance->work_date->format('n月j日')}}</td>
                </tr>
                <tr class="attendance-detail__row">
                    <th class="attendance-detail__header">出勤・退勤</th>
                    <td class="attendance-detail__description">
                        <input class="attendance-time__form" type="time" name="clock_in" value="{{$attendance->clock_in?->format('H:i')}}">
                        <div class="attendance-time__form">~</div>
                        <input class="attendance-time__form" type="time" name="clock_out" value="{{$attendance->clock_out?->format('H:i')}}">
                    </td>
                </tr>
                <tr class="attendance-detail__row">
                    <th class="attendance-detail__header">休憩</th>
                    <td class="attendance-detail__description">
                        <input class="break-time__form" type="time" name="break_start" value="{{$breakTime->get(0)?->break_start?->format('H:i')}}">
                        <div class="attendance-time__form">~</div>
                        <input class="break-time__form" type="time" name="break_end" value="{{$breakTime->get(0)?->break_end?->format('H:i')}}">
                    </td>
                </tr>
                <tr class="attendance-detail__row">
                    <th class="attendance-detail__header">休憩2</th>
                    <td class="attendance-detail__description">
                        <input class="break-time__form" type="time" name="break_start" value="{{$breakTime->get(1)?->break_start?->format('H:i')}}">
                        <div class="attendance-time__form">~</div>
                        <input class="break-time__form" type="time" name="break_end" value="{{$breakTime->get(1)?->break_end?->format('H:i')}}">
                    </td>
                </tr>
                <tr class="attendance-detail__row">
                    <th class="attendance-detail__header">備考</th>
                    <td class="attendance-detail__description">
                        <textarea class="textarea__form" name="reason">{{$attendanceRequest?->reason}}</textarea>
                    </td>
                </tr>
            </table>
            <button type="submit">修正</button>
        </form>
    @endif
</div>
@endsection