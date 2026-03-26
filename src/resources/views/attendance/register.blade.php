@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/users/attendance-register.js')
    @endif
@endsection

@section('content')
<div class="attendance-form">
    <div class="attendance-status">
        <div class="status-information">{{$status}}</div>
    </div>
    <div class="attendance-date">
        <div class="todays-date">{{$date->isoFormat('Y年M月D日(ddd)')}}</div>
    </div>
    <div class="attendance-time">
        <div class="current-time">{{$date->format('H:i')}}</div>
    </div>
    <div class="attendance-button">
        @if(empty($attendanceButtons) && empty($breakTimeButtons))
            <p class="attendance-message">お疲れ様でした</p>
        @else
        <form class="attendance-button" action="{{route('users.attendance.store')}}" method="post">@csrf
            @foreach($attendanceButtons as $attendanceButton)
                <button class="attendance-button__submit" value="{{$attendanceButton}}" type="submit" name="action">
                    {{$attendanceButton}}
                </button>
            @endforeach
            @foreach($breakTimeButtons as $breakTimeButton)
                <button class="breakTime-button__submit" value="{{$breakTimeButton}}" type="submit" name="action">
                    {{$breakTimeButton}}
                </button>
            @endforeach
        </form>
        @endif
    </div>
</div>
@endsection