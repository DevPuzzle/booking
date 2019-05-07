@extends('layouts.app')

@section('title', 'Page Content')

@section('content')
<div class="container mb-5">
    <div class="row">
        <div class="col-md-3">
            <h2>Create page</h2>
            <hr/>
            <a href="{{route('admin.pages.create')}}" class="btn btn-primary btn-block">Create</a>
            <h2 class="pt-3">Filter</h2>
            <hr/>
            
            <form method="GET" action="{{route('admin.pages.filtered')}}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="Categories">Categories:</label>
                    <select id="Categories" name="Categories" class="form-control custom-select">
                        <option value="all">All</option>
                        @foreach( $categories as $category )
                            <option value="{{ $category->id }}" 
                                    @if($selectedOptions['categories'] == $category->id ) selected @endif>
                                {{$category->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="regions">Regions:</label>
                    <select id="regions" name="regions" class="form-control custom-select">
                        <option value="all">All</option>
                        @foreach( $regions as $region )
                            <option value="{{ $region->id }}" 
                                    @if( $selectedOptions['regions'] == $region->id ) selected @endif>
                                    {{$region->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="Accessibleby">Accessible by:</label>
                    <select id="Accessibleby" name="Accessibleby" class="form-control custom-select">
                        <option value="all">Everyone</option>
                        @foreach( $roles as $role )
                            <option value="{{ $role->id }}" 
                                    @if( $selectedOptions['Accessibleby'] == $role->id ) selected @endif>
                                    {{$role->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status :</label>
                    <select id="status" name="status" class="form-control custom-select">
                        <option value="all">All</option>
                        @foreach( $statuses as $status )
                            <option value="{{ $status }}" 
                                    @if( $selectedOptions['status'] == $status ) selected @endif>
                                    {{ $status }}
                            </option>
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
            <h2>Pages</h2>
            <hr/>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Categories</th>
                        <th>Accessible by</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach ($pages as $page)
                    <tr>
                        <td>{{ $page->id }}</td>
                        <td>{{ $page->title }}</td>
                        <td class="">{{ $page->status }}
                            <span class="float-right"> 
                                @if(!$page->published_at)
                                <a class="btn btn-outline-success" href="{{route('admin.pages.publish', $page->id)}}" title="Publish">
                                    <span class="icon-eye"></span>
                                </a>
                                @else
                                <a class="btn btn-outline-warning" href="{{route('admin.pages.unpublish', $page->id)}}" title="Unpublish">
                                    <span class="icon-lock"></span>
                                </a>
                                @endif
                            </span>
                        </td>
                        <td class="text-center">{{ $page->categories_list }}</td>
                        <td>{!! $page->accessible_by !!} </td>
                        <td class="text-right">
                            <a class="btn btn-outline-primary" href="{{route('pages.show', $page->id)}}">
                                <span class="icon-book-open"></span>
                            </a>
                            <a class="btn btn-outline-primary" href="{{route('admin.pages.edit', $page->id) }}">
                                <span class="icon-pencil"></span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                @if ( isset($selectedOptions) )
                    {!! $pages->appends( $selectedOptions )->links() !!}
                @else
                    {!! $pages->links() !!}
                @endif
                
            </div>
        </div>
    </div>
</div>

@endsection
