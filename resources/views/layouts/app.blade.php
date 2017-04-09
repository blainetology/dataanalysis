<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <link href="/images/favicon.png" rel="shortcut icon">

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <link href="/css/font-awesome.min.css" rel="stylesheet">

    <!-- Scripts -->
    <script src="/js/app.js"></script>

    @yield('styles')
    @yield('scripts')

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container-fluid">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="/images/trackthatlogo.jpg" alt="{{ config('app.name', 'Track That Advisor') }}" style="height:42px; width:auto; margin-top:-10px;" />
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        @if(isset($client) && empty($isAdminView))
                        <li style="padding-top:15px;"> &nbsp; &nbsp; Client: <strong>{{ $client->business_name }}</strong></li>
                        @endif
                        @if(\Auth::check() && \Auth::user()->isEditor() && !empty($isAdminView))
                            <li><a href="/"><h4 style="padding:0; margin:2px 30px 0 0;">Site Administration</h4></a></li>
                            <li><a href="{{ route('adminclients.index') }}"><i class="fa fa-users" aria-hidden="true"></i> Clients</a></li>
                            <li><a href="{{ route('adminusers.index') }}"><i class="fa fa-user" aria-hidden="true"></i> Users</a></li>
                            <li><a href="{{ route('adminspreadsheets.index') }}"><i class="fa fa-table" aria-hidden="true"></i> Spreadsheets</a></li>
                            <li><a href="{{ route('reports.index') }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> Reports</a></li>
                        @endif
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">Login</a></li>
                            <li><a href="{{ url('/register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->displayname() }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ route('settings.edit',Auth::user()->id) }}">Settings</a></li>
                                    <li>
                                        <a href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> Logout </a>

                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')

        <footer style="text-align: center;">
        &copy;{{ date('Y') }}, Track That Advisor, LLC
        </footer>
    </div>

    <!-- Scripts -->
</body>
</html>
