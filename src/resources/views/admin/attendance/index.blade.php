@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/admin/attendance-index.js')
    @endif
@endsection

@section('content')
<p>ここは管理者ログイン後の画面です

</p>
@endsection