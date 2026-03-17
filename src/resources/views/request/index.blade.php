@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/users/request-index.js')
    @endif
@endsection

@section('content')
<p>ここはログイン後の画面です
    
</p>
@endsection