<?php
    $siteTitle = 'LaraCRM';
    if(function_exists('setting'))
        $siteTitle = setting('site.title', 'laraCRM');
?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/assets/font-awesome/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/animate.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/style.css') }}">
    @yield('custom_css')

</head>

<body class="gray-bg">

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>
                <a href="/">
                    <h2 class="logo-name">
                        {{$siteTitle}}
                    </h2>
                </a>
            </div>
            <div>
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="{{ URL::asset('/Inspina/js/jquery-3.1.1.min.js ') }}"></script>
    <script src="{{ URL::asset('/Inspina/js/bootstrap.min.js ') }}"></script>
    <script src="{{ URL::asset('/Inspina/js/plugins/toastr/toastr.min.js ') }}"></script>
    @yield('custom_js')
    <script>
        $(function () {
            var i = -1;
            var toastCount = 0;
            var $toastlast;
            var message = "{{session('message')}}";
            var type = "{{session('type')}}";
            if(message !== '')
            {
                toastr.options.timeOut = 10000;
                $("#toastrOptions").text("Command: toastr["
                        + type
                        + "](\""
                        + ''
                        + (message ? "\", \"" + message : '')
                        + "\")\n\ntoastr.options = "
                        + JSON.stringify(toastr.options, null, 2)
                );
                var $toast = toastr[type]('', message);
            }
        });
    </script>
</body>

</html>
