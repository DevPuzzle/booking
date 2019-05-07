@extends('layouts.app')

@section('title', 'Leave Days')

@section('content')
    @can('admin',auth()->user())
        @if($hasCalendars)
        <admin-guide-schedule></admin-guide-schedule>
        @else
            <p class="alert alert-info">
                You need to connect the proper calendars before using system. <a href="{{route('settings.index')}}">Set Calendars</a>
            </p>
        @endif
    @else
        <guide-schedule :user="{{auth()->user()}}"></guide-schedule>
    @endcan
@endsection
