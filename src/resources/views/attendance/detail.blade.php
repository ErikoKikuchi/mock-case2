@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/users/attendance-detail.js')
    @endif
@endsection

@section('content')
<p>ここはログイン後の詳細画面です
    
</p>
@endsection