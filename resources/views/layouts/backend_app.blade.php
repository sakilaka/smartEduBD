<!DOCTYPE html>
<html>

<head data-baseurl="{{ url('/') }}">
    <meta charset="utf-8">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel='icon' type='image/png' sizes='32x32' href="{{ app()->make('siteSettingObj')['favicon']??'' }}">
    <!-- no-cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}" />
    
    <script>
        window.laravel = {
            csrfToken: '{{ csrf_token() }}',
            baseurl: '{{ url('/') }}'
        }
    </script>

    <title>{{ config('app.name') }}</title>
    
    <link href="{{ asset('css/backend_layouts.css') }}" rel="stylesheet">
    <link async defer href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

</head>

<body>

    <div class="wrapper" id="app">
        <app />
    </div>

    <script src="{{ mix('js/app.js') }}"></script>
    <script async defer src="{{ asset('js/backend_layouts.js') }}"></script>

</body>

</html>