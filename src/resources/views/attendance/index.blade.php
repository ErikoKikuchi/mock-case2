@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/users/attendance-index.js')
    @endif
@endsection

@section('content')
<div class="attendance-list">
    <div class="attendance-list__title">| 勤怠一覧</div>
    <div class="attendance-list__month">
        <div class="attendance-list__previous">
            <a class="previous__month" href="{{route('users.attendance.list',['month'=>$previous->format('Y-m')])}}">←前月</a>
        </div>
        <div class="attendance-list__current">
            <img class="calender__logo" src="{{asset('/images/カレンダー.png')}}" type="image" name="logo">
            <div class="current__month">{{$date->isoFormat('Y/M')}}</div>
        </div>
        <div class="attendance-list__next">
            <a class="next__month" href="{{route('users.attendance.list',['month'=>$next->format('Y-m')])}}">翌月→</a>
        </div>
    </div>
    <table class="attendance-list__table">
        <tr class="attendance-list__row">
            <th class="attendance-list__header">日付</th>
            <th class="attendance-list__header">出勤</th>
            <th class="attendance-list__header">退勤</th>
            <th class="attendance-list__header">休憩</th>
            <th class="attendance-list__header">合計</th>
            <th class="attendance-list__header">詳細</th>
        </tr>
        @foreach($monthlyAttendances as $dailyAttendance)
            <tr class="attendance-list__row">
                <td class="attendance-list__description">{{$dailyAttendance->work_date->locale('ja')->isoFormat('M月D日(ddd)')}}</td>
                <td class="attendance-list__description">{{$dailyAttendance->clock_in?->format('H:i')}}</td>
                <td class="attendance-list__description">{{$dailyAttendance->clock_out?->format('H:i')}}</td>
                <td class="attendance-list__description">{{$dailyAttendance->breakTimeDisplay}}</td>
                <td class="attendance-list__description">{{$dailyAttendance->workTimeDisplay}}</td>
                <td class="attendance-list__description">
                    <a class="attendance-detail__link" href="{{route('users.attendance.detail',['id'=>$dailyAttendance->id])}}">詳細</a>
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endsection