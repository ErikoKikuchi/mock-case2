@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/admin/request-approve.js')
    @endif
@endsection

@section('content')
<p>
    ここは修正申請承認画面です
</p>
@endsection