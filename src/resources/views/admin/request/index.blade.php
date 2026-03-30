@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/admin/request-index.js')
    @endif
@endsection

@section('content')
<p>
    ここは管理者申請一覧画面です
</p>
@endsection