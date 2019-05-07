@extends('layouts.app')

@section('title', 'Page Content')

@section('content')
    <h3>{!! $page->title !!}</h3>
    <p>{!! $page->summary !!}</p>
    <p>{!! $page->content !!}</p>
    <p><a href="/admin/pages/{!!$page->id!!}/edit">edit</a></p>
    <p><a href="/admin/pages/{!!$page->id!!}/delete">delete</a></p>
    <form action="/admin/pages/{{ $page->id }}" method="post">
        @csrf
        <input name="_method" type="hidden" value="delete">
        <input value="delete" type="submit" class="btn btn-danger">
    </form>
    @isset($page->published_at)
        <p>published</p>
    @else
        <p><a href="/admin/pages/{!!$page->id!!}/publish">publish</a></p>
    @endif
@endsection
