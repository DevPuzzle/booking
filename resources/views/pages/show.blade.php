@extends('layouts.app')

@section('title', 'Page Content')

@section('content')
<div class="container">
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{route(auth()->user()->is_admin ? 'admin.pages.index' : 'pages.index')}}">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$page->title}}</li>
            </ol>
        </nav>
        <div class="col-md-3">

        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-auto">
                            <h1 class="card-title mb-0">{{ $page->title }}</h1>
                            <small class="text-muted mr-3">
                                <strong>Last update</strong> {{$page->updated_at->diffForHumans()}}
                            </small>
                            <small class="text-muted">
                                <strong>Categories: </strong> {!! $page->categories_list !!}
                            </small>

                        </div>
                        <div class="col">
                            @if(auth()->user()->is_admin)
                            <div class="float-right">
                                <div class="btn-toolbar">
                                    <div class="btn-group">
                                        <a class="btn btn-outline-primary"
                                           href="{{route('admin.pages.edit', $page->id)}}"><span class="icon-pencil"></span>
                                            Edit</a>
                                        @if(!$page->published_at)
                                        <a class="btn btn-outline-primary"
                                           href="{{route('admin.pages.publish', $page->id)}}"><span
                                                class="icon-lock-open"></span> Publish</a>
                                        @else
                                        <a class="btn btn-outline-primary"
                                           href="{{route('admin.pages.unpublish', $page->id)}}"><span
                                                class="icon-lock"></span> Unpublish</a>
                                        @endif
                                        <a class="btn btn-outline-primary"
                                           href="{{route('admin.pages.send', $page->id)}}"><span
                                                class="icon-envelope"></span> Send</a>
                                    </div>
                                    <div class="btn-group ml-3">
                                        <a class="btn btn-danger text-white"><span class="icon-trash"></span> Delete</a>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <p class="lead">{{ $page->summary }}</p>
                    <div>{!! $page->content !!}</div>
                </div>
                {{--<div class="card-footer">-                -}}
            {{--<read-by page_id={{ $page->id}}></read-by>--}}
                        {{--</div>--}}
                    </div>
                </div>
            </div>
        </div>



        @endsection
