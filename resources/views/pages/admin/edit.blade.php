@extends('layouts.app')

@section('title', 'Page Content')

@section('content')
<!--    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{route( 'admin.pages.index' )}}">Pages</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <a href="{{route('pages.show' ,  $page->id)}}">{{$page->title}}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>-->
    <ck-editor-edit :page_id="{{ $page->id }}"></ck-editor-edit>
@endsection
