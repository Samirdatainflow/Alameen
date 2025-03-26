<html>
    <head>
        <title>@yield('title')</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Language" content="en">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
        <meta name="description" content="">
        <!-- Disable tap highlight on IE -->
        <meta name="msapplication-tap-highlight" content="no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @include('layout.stylesheet')
        <script>
            var base_url="{{url::to('/')}}";
        </script>
    </head>
    <body>
        @yield('content')
        @include('layout.script')
        <script src="{{ URL::asset('public/backend/page_js/auth_script.js')}}" type="text/javascript"></script>
    </body>
</html>