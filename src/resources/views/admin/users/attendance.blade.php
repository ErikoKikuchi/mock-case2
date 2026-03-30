@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/admin/user-attendance.js')
    @endif
@endsection

@section('content')
<div class="content">
    <div class ="attendance-list">
        <div class="staff-attendance__title">| {{$name->name}}さんの勤怠 </div>
        <div class="attendance-list__month">
            <div class="attendance-list__previous">
                <a class="previous__month" href="{{route('each.staff.attendance',['id' => $name->id, 'month'=>$previous->format('Y-m')])}}">←前月</a>
            </div>
            <div class="attendance-list__current">
                <img class="calender__logo" src="{{asset('/images/カレンダー.png')}}" type="image" name="logo">
                <div class="current__month">{{$date->format('Y/m')}}</div>
            </div>
            <div class="attendance-list__next">
                <a class="next__month" href="{{route('each.staff.attendance',['id' => $name->id, 'month'=>$next->format('Y-m')])}}">翌月→</a>
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
            @foreach($calendar as $item)
                <tr class="attendance-list__row">
                    <td class="attendance-list__description">{{$item['date']->locale('ja')->isoFormat('MM月DD日(ddd)')}}</td>
                    <td class="attendance-list__description">{{$item['attendance']?->clock_in?->format('H:i')}}</td>
                    <td class="attendance-list__description">{{$item['attendance']?->clock_out?->format('H:i')}}</td>
                    <td class="attendance-list__description">{{$item['attendance']?->breakTimeDisplay}}</td>
                    <td class="attendance-list__description">{{$item['attendance']?->workTimeDisplay}}</td>
                    <td class="attendance-list__description">
                        @if($item['attendance'])
                            <a class="attendance-detail__link" href="{{route('users.attendance.detail',['id'=>$item['attendance']->id])}}">詳細</a>
                        @else
                            <a class="attendance-detail__link" href="{{route('users.attendance.detail',['date'=>$item['date']->toDateString()])}}">詳細</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection