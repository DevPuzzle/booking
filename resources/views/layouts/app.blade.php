<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Torontour - @yield('title')</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    </head>
    <body>
        <div class="bg-faded">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light ">
                    <!--<a class="navbar-brand" href="/">{{config('app.name')}}</a>-->
                    <a class="navbar-brand" href="/"><img class="" src="/images/NYTours.png" alt="{{config('app.name')}}"></a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item active">
                                <a class="nav-link" href="/leavedays">Leave Days <span class="sr-only">(current)</span></a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="/timesheets">Timesheets</a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="{{ auth()->user()->is_admin ? route('admin.pages.index') : route('pages.index') }}">Pages</a>
                            </li>
                        </ul>
                        <div class="navbar-nav my-2 my-lg-0">
                            <li class="nav-item dropdown">

                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{auth()->user()->name}} (role: {{ auth()->user()->role }} )
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('settings.index') }}">Settings</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                                </div>

                            </li>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <div id="app" class="container">
            @yield('content')
            <notifications group="notifications" classes="notification-styles"/>
        </div>
        <!-- Scripts -->
        <script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
