@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/admin/attendance-index.js')
    @endif
@endsection

@section('content')
<div class="content">
    <div class ="attendance-list">
        <div class="admin-attendance-list__title">| {{$date->format('Y年m月d日')}}の勤怠 </div>
        <div class="attendance-list__day">
            <div class="attendance-list__previous">
                <a class="previous__day" href="{{route('attendance.list',['day'=>$previous->toDateString()])}}">←前日</a>
            </div>
            <div class="attendance-list__today">
                <img class="calender__logo" src="{{asset('/images/カレンダー.png')}}" type="image" name="logo">
                <div class="today">{{$date->format('Y/m/d')}}</div>
            </div>
            <div class="attendance-list__next">
                <a class="next__day" href="{{route('attendance.list',['day'=>$next->toDateString()])}}">翌日→</a>
            </div>
        </div>
        <table class="attendance-list__table">
            <tr class="attendance-list__row">
                <th class="attendance-list__header">名前</th>
                <th class="attendance-list__header">出勤</th>
                <th class="attendance-list__header">退勤</th>
                <th class="attendance-list__header">休憩</th>
                <th class="attendance-list__header">合計</th>
                <th class="attendance-list__header">詳細</th>
            </tr>
            @foreach($calendar as $item)
                <tr class="attendance-list__row">
                    <td class="attendance-list__description">{{$item['staff']->name}}</td>
                    <td class="attendance-list__description">{{$item['attendance']?->clock_in?->format('H:i')}}</td>
                    <td class="attendance-list__description">{{$item['attendance']?->clock_out?->format('H:i')}}</td>
                    <td class="attendance-list__description">{{$item['attendance']?->breakTimeDisplay}}</td>
                    <td class="attendance-list__description">{{$item['attendance']?->workTimeDisplay}}</td>
                    <td class="attendance-list__description">
                        @if($item['attendance'])
                            <a class="attendance-detail__link" href="{{route('admin.attendance.detail',['id'=>$item['attendance']->id])}}">詳細</a>
                        @else
                            <a class="attendance-detail__link" href="{{route('admin.attendance.detail',['date'=>$date->toDateString(), 'user_id'=>$item['staff']->id])}}">詳細</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>

@endsection