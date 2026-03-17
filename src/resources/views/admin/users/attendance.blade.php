@extends('layouts.app')

@section('css')
    @if(!app()->environment(['testing']) && !config('app.vite_disabled'))
        @vite('resources/js/admin/user-attendance.js')
    @endif
@endsection

@section('content')
<p>
    
</p>
@endsection