@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/admin/user-index.js')
    @endif
@endsection

@section('content')
<div class="content">
    <div class="admin-content">
        <h1 class="staff-index__title">| スタッフ一覧</h1>
        <div class="staff-index__inner">
            <table class="staff-index">
                <tr class ="staff-index__row">
                    <th class="staff-index__header">名前</th>
                    <th class="staff-index__header">メールアドレス</th>
                    <th class="staff-index__header">月次勤怠</th>
                </tr>
                @foreach($staffs as $staff)
                    <tr class ="staff-index__row">
                        <td class="staff-index__description">
                            <div class="staff-name">
                                {{$staff->name}}
                            </div>
                        </td>
                        <td class="staff-index__description">
                            <div class="staff-email">
                                {{$staff->email}}
                            </div>
                        </td>
                        <td class="staff-index__description">
                            <div class="staff-detail">
                                <a class="staff-detail__button" href="{{route('each.staff.attendance',['id'=>$staff->id])}}">詳細</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection