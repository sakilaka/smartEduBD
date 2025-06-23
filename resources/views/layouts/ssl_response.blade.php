<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{URL::to('/')}}" />
    <link rel='icon' type='image/png' sizes='32x32' href="{{asset('images/favicon.png')}}">
    <script>
        window.laravel = {
            csrfToken: '{{ csrf_token() }}',
            baseurl: '{{URL::to("/")}}'
        }
    </script>
    <title>{{ config('app.name') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/frontend_layouts.css') }}" rel="stylesheet">
    <link href="{{ asset('web/boxicons/css/boxicons.min.css') }}" rel="stylesheet">

</head>

<body style="position:relative">

@if(request()->query('status') == 'success')
    @include('layouts.partials.success')

@elseif(request()->query('status') == 'cancel')
 @include('layouts.partials.cancel')

@elseif(request()->query('status') == 'fail')
 @include('layouts.partials.failed')
@endif

</body>
</html>