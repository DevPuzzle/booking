@extends('layouts.app')

@section('title', 'Leave Days')

@section('content')
        <guide-schedule :user="{{auth()->user()}}"></guide-schedule>
@endsection
