@extends('layouts.app')

@section('title', 'Timesheets')

@section('content')
    @can('admin',auth()->user())
        <admintimesheet></admintimesheet>
    @else
        <timesheet :user="{{$user}}"></timesheet>
    @endcan
@endsection
