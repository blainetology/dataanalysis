<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Styles -->
    <link href="http://data.app/css/app.css" rel="stylesheet">

    <script src="http://data.app/js/app.js"></script>

    @yield('styles')
    @yield('scripts')

</head>
<body>
    <div id="app">

        @yield('content')

    </div>

    <!-- Scripts -->
</body>
</html>
