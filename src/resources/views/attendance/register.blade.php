@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/users/attendance-register.js')
    @endif
@endsection

@section('content')
@endsection