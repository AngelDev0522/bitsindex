<?php
    $siteTitle = 'LaraCRM';
    if(function_exists('setting'))
        $siteTitle = setting('site.title', 'laraCRM');
?>
<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/assets/font-awesome/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/plugins/iCheck/custom.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/plugins/summernote/summernote.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/plugins/summernote/summernote-bs3.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/animate.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/style.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/css/simple-sidebar.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/css/sweetalert.css') }}">
    <!-- Toastr style -->
    <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/plugins/toastr/toastr.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css" />

    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>

    </script>
</head>

<body>
    <div id="wrapper">
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="{{url('/')}}" class="site_title"><i class="fa fa-paw"></i> <span>{{$siteTitle}}</span></a>
                </div>
                    <div class="dropdown profile-element"> <span>
                        <?php
                        // URL::asset('/storage/'.Auth::user()->avatar).(isset($hard_reload) ? '?v=')
                        $avatarURL = URL::asset('/storage/'.Auth::user()->avatar);
                        if(isset($hard_reload) && $hard_reload)
                            $avatarURL .= '?v='.strval(rand());
                        ?>
                            <img alt="image" class="img-circle profile_img" src="{{ $avatarURL }}" />
                             </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">Welcome</strong>
                             </span> <span class="text-muted text-xs block">{{ Auth::user()->name }} <b class="caret"></b></span> </span> </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="{{url('profile')}}">Profile</a></li>
                            <li><a href="{{url('inbox')}}">Mailbox</a></li>
                            <li class="divider"></li>
                            <li><a href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                        </ul>
                    </div>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                    <div class="logo-element">
                        LCRM
                    </div>
                </li>
                @if (Auth::user()->enable_wallet == 1)
                    <li class="{{Request::is('wallet/litecoin') ? 'active' : ''}}"><a href="{{url('wallet/litecoin')}}"><i class="fa fa-diamond"></i><span class="nav-label">Litecoin</span></a></li>
                    <li class="{{Request::is('wallet/peercoin') ? 'active' : ''}}"><a href="{{url('wallet/peercoin')}}"><i class="fa fa-diamond"></i><span class="nav-label">Peercoin</span></a></li>
                    <li class="{{Request::is('wallet/ripple') ? 'active' : ''}}"><a href="{{url('wallet/ripple')}}"><i class="fa fa-diamond"></i><span class="nav-label">Ripple</span></a></li>
                @endif
                <h3 class="text-center">General</h3>
                <ul class="nav side-menu">
                    <li class="{{Request::is('profile') ? 'active' : ''}}"><a href="{{url('profile')}}"><i class="fa fa-user"></i><span class="nav-label">Profile</span></a></li>
                    @if (Auth::user()->enable_chat == 1)
                    <li class="{{Request::is('chat') ? 'active' : ''}}"><a href="{{url('chat')}}"><i class="fa fa-address-book"></i><span class="nav-label">Chat</span></a></li>
                    @endif
                    @if (Auth::user()->enable_calendar == 1)
                    <li class="{{Request::is('event/add') ? 'active' : ''}}{{Request::is('event') ? 'active' : ''}}{{Request::is('event/remove') ? 'active' : ''}}">
                        <a href="#"><i class="fa fa-calendar"></i> <span class="nav-label">Calendar</span> <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li class="{{Request::is('event/add') ? 'active' : ''}}"><a href="{{url('event/add')}}">Add Task</a></li>
                            <li class="{{Request::is('event') ? 'active' : ''}}"><a href="{{url('event')}}">Full Calendar</a></li>
                            <li class="{{Request::is('event/remove') ? 'active' : ''}}"><a href="{{url('event/remove')}}">Remove Task</a></li>
                        </ul>
                    </li>
                    @endif
                    @if (Auth::user()->enable_email == 1)
                    <li class="{{Request::is('inbox') ? 'active' : ''}}{{Request::is('sentmail') ? 'active' : ''}}{{Request::is('compose') ? 'active' : ''}}">
                        <a href="#"><i class="fa fa-envelope"></i> <span class="nav-label">Mailbox </span> <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li class="{{Request::is('inbox') ? 'active' : ''}}"><a href="{{url('inbox')}}">Inbox</a></li>
                            <li class="{{Request::is('sentmail') ? 'active' : ''}}"><a href="{{url('sentmail')}}">Sent email</a></li>
                            <li class="{{Request::is('compose') ? 'active' : ''}}"><a href="{{url('compose')}}">Compose email</a></li>
                        </ul>
                    </li>
                    @endif
                </ul>

            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i
                                class="fa fa-bars"></i> </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li class="dropdown">
                            <a href="{{ url('/inbox') }}" class="dropdown-toggle count-info"></a>
                        </li>
                        <li>
                            <a href="{{ url('/logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out"></i> Log out
                            </a>
                        </li>
                    </ul>

                </nav>
            </div>

            @include('helper.flashMessage')
            <div class="wrapper wrapper-content animated fadeIn">

                @yield('content')

            </div>

            <div class="footer">
                <!-- <div class="pull-right">
                10GB of <strong>250GB</strong>
            </div> -->
                <div>
                    <strong>Copyright</strong> Brad Kartel &copy; 2019
                </div>
            </div>
        </div>

    </div>



    <!-- Mainly scripts -->
    <!-- <script src="{{ URL::asset('/Inspina/js/jquery-3.1.1.min.js ') }}"></script> -->





    <!-- <script src="{{ URL::asset('/Inspina/js/bootstrap.min.js ') }}"></script> -->

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>

    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>   -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>

    <script src="{{ URL::asset('/Inspina/js/plugins/metisMenu/jquery.metisMenu.js ') }}"></script>
    <script src="{{ URL::asset('/Inspina/js/plugins/slimscroll/jquery.slimscroll.min.js ') }}"></script>
    <!-- Flot -->
    <script src="{{ URL::asset('/Inspina/js/plugins/flot/jquery.flot.js ') }}"></script>
    <script src="{{ URL::asset('/Inspina/js/plugins/flot/jquery.flot.tooltip.min.js ') }}"></script>
    <script src="{{ URL::asset('/Inspina/js/plugins/flot/jquery.flot.spline.js ') }}"></script>
    <script src="{{ URL::asset('/Inspina/js/plugins/flot/jquery.flot.resize.js ') }}"></script>
    <script src="{{ URL::asset('/Inspina/js/plugins/flot/jquery.flot.pie.js ') }}"></script>
    <script src="{{ URL::asset('/Inspina/js/plugins/flot/jquery.flot.symbol.js ') }}"></script>
    <script src="{{ URL::asset('/Inspina/js/plugins/flot/jquery.flot.time.js ') }}"></script>
    <script src="{{ URL::asset('/Inspina/js/plugins/iCheck/icheck.min.js ') }}"></script>
    <!-- Sparkline -->
    <script src="{{ URL::asset('/Inspina/js/plugins/sparkline/jquery.sparkline.min.js ') }}"></script>

    <script src="{{ URL::asset('/Inspina/js/plugins/summernote/summernote.min.js ') }}"></script>
    <script src="{{ URL::asset('/Inspina/js/plugins/pace/pace.min.js ') }}"></script>
    <script src="{{ URL::asset('/Inspina/js/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js ') }}"></script>
<!-- Toastr script -->
<script src="{{ URL::asset('/Inspina/js/plugins/toastr/toastr.min.js ') }}"></script>
    <!-- Custom and plugin javascript -->
    <script src="{{ URL::asset('/Inspina/js/inspinia.js ') }}"></script>



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
        })
        $(document).ready(function() {
            // icheck setup
            $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var sparklineCharts = function(){
                $("#sparkline1").sparkline([34, 43, 43, 35, 44, 32, 44, 52], {
                    type: 'line',
                    width: '100%',
                    height: '50',
                    lineColor: '#1ab394',
                    fillColor: "transparent"
                });

                $("#sparkline2").sparkline([32, 11, 25, 37, 41, 32, 34, 42], {
                    type: 'line',
                    width: '100%',
                    height: '50',
                    lineColor: '#1ab394',
                    fillColor: "transparent"
                });

                $("#sparkline3").sparkline([34, 22, 24, 41, 10, 18, 16,8], {
                    type: 'line',
                    width: '100%',
                    height: '50',
                    lineColor: '#1C84C6',
                    fillColor: "transparent"
                });
            };

            var sparkResize;

            $(window).resize(function(e) {
                clearTimeout(sparkResize);
                sparkResize = setTimeout(sparklineCharts, 500);
            });

            sparklineCharts();




            var data1 = [
                [0,4],[1,8],[2,5],[3,10],[4,4],[5,16],[6,5],[7,11],[8,6],[9,11],[10,20],[11,10],[12,13],[13,4],[14,7],[15,8],[16,12]
            ];
            var data2 = [
                [0,0],[1,2],[2,7],[3,4],[4,11],[5,4],[6,2],[7,5],[8,11],[9,5],[10,4],[11,1],[12,5],[13,2],[14,5],[15,2],[16,0]
            ];
            $("#flot-dashboard5-chart").length && $.plot($("#flot-dashboard5-chart"), [
                        data1,  data2
                    ],
                    {
                        series: {
                            lines: {
                                show: false,
                                fill: true
                            },
                            splines: {
                                show: true,
                                tension: 0.4,
                                lineWidth: 1,
                                fill: 0.4
                            },
                            points: {
                                radius: 0,
                                show: true
                            },
                            shadowSize: 2
                        },
                        grid: {
                            hoverable: true,
                            clickable: true,

                            borderWidth: 2,
                            color: 'transparent'
                        },
                        colors: ["#1ab394", "#1C84C6"],
                        xaxis:{
                        },
                        yaxis: {
                        },
                        tooltip: false
                    }
            );

            $('a.dropdown-toggle').bind("click", function() {
                $(this).parent().toggleClass("open");
                $(this).attr("aria-expanded", !$(this).attr("aria-expanded"));
            })
        });
    </script>
    @yield('scripts')
</body>

</html>

