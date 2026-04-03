@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/users/attendance-detail.js')
    @endif
@endsection

@section('content')
<div class="content">
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
            <div class="request-alert">
                <p class="alert-message">*承認待ちのため修正はできません</p>
            </div>
        @else
            <form class="attendance-request" action="{{route('users.attendance.request')}}" method="POST">@csrf
                <input type="hidden" name="attendance_id" value="{{$attendance->id}}">
                <table class="attendance-detail__table">
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__header">名前</th>
                        <td class="attendance-detail__description">{{$user->name}}
                        </td>
                    </tr>
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__header">日付</th>
                        <td class="attendance-detail__description">{{$attendance->work_date->format('Y年')}}</td>
                        <td class="attendance-detail__form"></td>
                        <td class="attendance-detail__description">
                        {{$attendance->work_date->format('n月j日')}}</td>
                    </tr>
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__header">出勤・退勤</th>
                        <td class="attendance-detail__description" >
                            <div class="description__input">
                                <input class="attendance-time__form" type="time" name="clock_in" value="{{old('clock_in',$attendance->clock_in?->format('H:i'))}}" >
                                <div class="error">
                                    @error('clock_in')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </td>
                        <td class="attendance-detail__form">~</td>
                        <td class="attendance-detail__description" >
                            <div class="description__input">
                                <input class="attendance-time__form" type="time" name="clock_out" value="{{old('clock_out',$attendance->clock_out?->format('H:i'))}}" >
                                <div class="error">
                                    @error('clock_out')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__header">休憩</th>
                        <td class="attendance-detail__description" >
                            <div class="description__input">
                                <input class="break-time__form" type="time" name="break_start[]" value="{{old('break_start.0',$breakTime->get(0)?->break_start?->format('H:i'))}}" >
                                <div class="error">
                                    @error('break_start.0')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </td>
                        <td class="attendance-detail__form">~</td>
                        <td class="attendance-detail__description" >
                            <div class="description__input">
                                <input class="break-time__form" type="time" name="break_end[]" value="{{old('break_end.0',$breakTime->get(0)?->break_end?->format('H:i'))}}" >
                                <div class="error">
                                    @error('break_end.0')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__header">休憩2</th>
                        <td class="attendance-detail__description" >
                            <div class="description__input">
                                <input class="break-time__form" type="time" name="break_start[]" value="{{old('break_start.1',$breakTime->get(1)?->break_start?->format('H:i'))}}" >
                                <div class="error">
                                    @error('break_start.1')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </td>
                        <td class="attendance-detail__form">~</td>
                        <td class="attendance-detail__description" >
                            <div class="description__input">
                                <input class="break-time__form" type="time" name="break_end[]" value="{{old('break_end.1',$breakTime->get(1)?->break_end?->format('H:i'))}}" >
                                <div class="error">
                                    @error('break_end.1')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__header">備考</th>
                        <td class="attendance-detail__description" colspan="3">
                            <textarea class="textarea__form" name="reason">{{old('reason',$attendanceRequest?->reason)}}</textarea>
                            <div class="error">
                                @error('reason')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </td>
                    </tr>
                </table>
                <button class="request-button" type="submit">修正</button>
            </form>
        @endif
    </div>
</div>
@endsection