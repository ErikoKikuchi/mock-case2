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
        <form class="attendance-button" action="post">
            @foreach($attendanceButtons as $attendanceButton)
                <button class="attendance-button__submit" type="submit">
                    {{$attendanceButton}}
                </button>
            @endforeach
        </form>
    </div>
</div>
@endsection