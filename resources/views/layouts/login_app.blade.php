<!DOCTYPE html>
<html>

<head data-baseurl="{{URL::to('/')}}">
    <meta charset="utf-8">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel='icon' type='image/png' sizes='32x32' href="{{ app()->make('siteSettingObj')['favicon']??'' }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}" />
    <script>
        window.laravel = {
            baseurl: "{{ url('/') }}",
            app_name: '{{ config('app.name') }}'
        }
    </script>
    <title>{{ config('app.name') }}</title>
    
    <!-- Styles -->
    <link href="{{ asset('css/login_layouts.css') }}" rel="stylesheet">

</head>

<body>

    <div class="form-body" id="app">
        <!-- main content -->
        @yield('content')
        <!-- /main content -->
    </div>

    <!-- App js -->
    <script src="{{ mix('js/login_app.js') }}"></script>
</body>

</html>