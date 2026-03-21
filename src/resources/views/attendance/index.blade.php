@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/users/attendance-index.js')
    @endif
@endsection

@section('content')
<div class="attendance-form">
    <div class="attendance-status">
        <div class="status-information">{{$status}}</div>
    </div>
    <div class="attendance-date">
        <div class="todays-date">{{$date->isoformat('Y年M月D日(ddd)')}}
    </div>
    <div class="attendance-time">
        <div class="current-time">{{$date->format('H:i')}}</div>
    </div>
    <div class="attendance-button">
        @if(empty($attendanceButtons))
            <p>お疲れ様でした</p>
        @else
        <form class="attendance-button" action="{{route('users.attendance.store')}}" method="post">@csrf
            @foreach($attendanceButtons as $attendanceButton)
                <button class="attendance-button__submit" value="{{$attendanceButton}}" type="submit" name="action">
                    {{$attendanceButton}}
                </button>
            @endforeach
        </form>
        @endif
    </div>
</div>
@endsection