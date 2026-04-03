@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/admin/request-approve.js')
    @endif
@endsection

@section('content')
<div class="content">
    <div class="attendance-detail">
        <div class="attendance-detail__title">| 勤怠詳細</div>
            <form action="{{route('update.attendance')}}" method="post">@csrf
                <table class="attendance-detail__table">
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__header">名前</th>
                        <td class="attendance-detail__description">{{$user->name}}</td>
                    </tr>
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__header">日付</th>
                        <td class="attendance-detail__description">{{$attendance->work_date->format('Y年')}}</td>
                        <td class="attendance-detail__form"></td>
                        <td class="attendance-detail__description">{{$attendance->work_date->format('n月j日')}}</td>
                    </tr>
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__header">出勤・退勤</th>
                        <td class="attendance-detail__description">{{ $attendanceRequest->clock_in_value}}</td>
                        <td class="attendance-detail__form">~</td>
                        <td class="attendance-detail__description">
                            {{ $attendanceRequest->clock_out_value}}</td>
                    </tr>
                    @foreach($attendanceRequest->break_items as $break)
                        <tr class="attendance-detail__row">
                            <th class="attendance-detail__header">休憩</th>
                            <td class="attendance-detail__description">{{$break['break_start'] }}</td>
                            <td class="attendance-detail__form">~</td>
                            <td class="attendance-detail__description">{{$break['break_end']}}</td>
                        </tr>
                    @endforeach
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__header">備考</th>
                        <td class="attendance-detail__textarea" colspan="3">{{$attendanceRequest?->reason}}</td>
                    </tr>
                </table>
                    <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
                    <input type="hidden" name="attendance_request_id" value="{{ $attendanceRequest->id }}">
                @if($attendance->status==='pending')
                    <div class="approve">
                        <button class="approve-button" type="submit">承認</button>
                    </div>
                @else
                    <div class="approved">承認済み
                    <div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection