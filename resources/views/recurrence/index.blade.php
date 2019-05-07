@extends('layouts.app')
@section('content')
    <leave-day-list username="{{$username}}" :is-admin="{{ auth()->user()->isAdmin ? 'true' : 'false'}}"></leave-day-list>
@endsection