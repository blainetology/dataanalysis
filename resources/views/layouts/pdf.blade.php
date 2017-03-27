<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <link href="http:/{{ $_SERVER['HTTP_HOST'] }}/images/favicon.png" rel="shortcut icon">

    <!-- Styles -->
    <link href="http://{{ $_SERVER['HTTP_HOST'] }}/css/app.css" rel="stylesheet">

    @yield('styles')
    @yield('scripts')

</head>
<body>
    <div id="app">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <br/><br/><br/>
                        <br/><br/><br/>
                        <img src="http://{{ $_SERVER['HTTP_HOST'] }}/images/trackthatlogo.jpg" alt="{{ config('app.name', 'Track That Advisor') }}" />
                        <br/><br/><br/>
                        <br/><br/><br/>
                        <br/><br/><br/>
                        <p>custom report prepared for</p>
                        <h2>{{ $report->client->business_name }}</h2>
                        <p>Represents data through {{ date('m/d/Y') }}</p>
                    </div>
                </div>
            </div>

        @yield('content')

    </div>

    <!-- Scripts -->
</body>
</html>
