@extends('layouts.app')

@section('title', 'Page Content')

@section('content')
<div class="container">
    <h1>Torontour Resources</h1>
        <hr/>
<!--        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Resources</li>
            </ol>
        </nav>-->
    <div class="row">
        
        
        <div class="col-md-3">
            <h4>Page filter</h4>
            <hr/>
             <form method="POST" action="{{route('pages.filtered')}}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="Categories">Categories:</label>
                    <select id="Categories" name="Categories" class="form-control custom-select">
                        <option value="all">All</option>
                        @foreach( $categories as $category )
                            <option value="{{ $category->id }}" 
                                    @if($selectedOptions['categories'] == $category->id ) selected @endif
                            >{{$category->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="regions">Regions:</label>
                    <select id="regions" name="regions" class="form-control custom-select">
                        <option value="all">All</option>
                        @foreach( $regions as $region )
                            <option value="{{ $region->id }}"
                                    @if($selectedOptions['regions'] == $region->id ) selected @endif
                                    >{{$region->name}}</option>
                        @endforeach
                    </select>
                </div>
                                
                <div class="form-group">
                    <button type="submit" id="status_select" name="search" class="form-control btn btn-primary btn-block">
                       Search 
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-9">
             @foreach ($pages as $page)
             <div class="card mb-2">
                <div class="card-header">
                    <h4 class="mt-2"><b>{{$page->id}}</b> : {{$page->title}}</h4>
                    <p class="card-subtitle text-right"><strong>Categories:</strong>{{$page->categories_list}}</p>
                    <p class="card-subtitle text-right"><strong>Page region:</strong>
                        @foreach($page->regions as $region)
                            {{$region->name}}<br/>
                        @endforeach
                    </p>
                </div>
                <div class="card-body" style="height: 200px; overflow-y: hidden;">
                    <p class="card-text ">{!! $page->content !!}</p>
                </div>
                <div class="card-footer">
                    <div class=" d-flex justify-content-between align-content-center">
                        <span>Last updated: {{$page->updated_at->diffForHumans()}}</span>
                        <span class="text-right">
                            <a href="/pages/{{ $page->id }}" class="btn btn-success">
                                Read More
                            </a>

                        </span>
                    </div>
                </div>
            </div>
        @endforeach
        @if ( isset($selectedOptions) )
            {!! $pages->appends( $selectedOptions )->links() !!}
        @else
            {!! $pages->links() !!}
        @endif
        </div>
    </div>
</div>

@endsection
