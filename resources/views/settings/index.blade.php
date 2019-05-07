@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <div id="settings" class="container">
        {{--<edit-user-component :user="{{$user}}" update-url="{{route('settings.user.update')}}"></edit-user-component>--}}
        <br>
        @if(isset($calendars))
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Calendar Settings</h5>
                </div>

                <ul class="list-group list-group-flush">
                    @if(isset($calendars['read_calendar']['id']))
                        <li class="list-group-item">
                            Read Calendar: {{ $calendars['read_calendar']['name']}} {{ $calendars['read_calendar']['summary']}}
                            <a class="btn-link btn-default float-right" href="{{route('settings.sync-calendar')}}">Sync
                                Calendar.</a>
                        </li>
                    @else
                        <li class="list-group-item">
                            Read Calendar: Not Set
                        </li>
                    @endif
                    @if(isset($calendars['write_calendar']['id']))
                        <li class="list-group-item">
                            Write Calendar: {{ $calendars['write_calendar']['name']}} {{ $calendars['write_calendar']['summary']}}
                            <a class="btn-link btn-default float-right disabled">Sync Calendar.</a>
                        </li>
                    @else
                        <li class="list-group-item">
                            Write Calendar: Not Set
                        </li>
                    @endif
                </ul>
                <div class="card-body">
                    <a class="btn-link btn-default" href="{{route('google.redirect')}}">Connect To Google.</a>
                </div>
            </div>
        @endif
        @if(isset($update_calendars))
            <edit-calendar-component :calendars="{{$update_calendars}}"
                                     update-url="{{route('settings.calendar.update')}}"
                                     settings-page-path="{{route('settings.index')}}"
                                     gcal-email="{{$gcalEmail}}"></edit-calendar-component>
        @endif
    </div>

@endsection
