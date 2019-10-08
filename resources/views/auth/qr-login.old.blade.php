<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Crypto currency authentication">
    <meta name="author" content="{{$siteTitle}}">
    <meta name="apple-mobile-web-app-title" content="{{$siteTitle}}">

    <title>{{$siteTitle}}</title>
    <link rel="icon" type="image/png" href="{{ URL::asset('/img/qrscan/drawable-xhdpi-icon.png') }}" sizes="96x96">
    <link rel="apple-touch-icon" href="{{URL::asset('/img/qrscan/drawable-xhdpi-icon.png')}}">
    <link rel="apple-touch-icon" sizes="96x96" href="{{ URL::asset('/img/qrscan/drawable-xhdpi-icon.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ URL::asset('/img/qrscan/drawable-xxhdpi-icon.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ URL::asset('/img/qrscan/drawable-xxxhdpi-icon.png') }}">
    <link rel="manifest" href="manifest.json">

    <!-- Bootstrap core CSS -->
    {{-- <link rel="stylesheet" href="{{ URL::asset('/Inspina/css/bootstrap.min.css') }}"> --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ URL::asset('/css/style_carbon.css') }}">

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script src="{{ URL::asset('/js/localforage.js') }}"></script>
    <script>
        // Globals for the scanner. Should look at reducing this.
        const QRScanner = {};
        QRScanner.scannerIsRunning = false;
        QRScanner.player = null;
        QRScanner.localMediaStream = null;
        QRScanner.context = null;
        QRScanner.canvas = null;

        // =======================================================================
        // Use JQuery to create a lightweight UI.
        // Basically when the user clicks a link we show a div and hide others.
        // =======================================================================

        $(document).ready(function() {

            $('#scan-button').click(function() {
                $('.page').hide();
                $('#scanner').show();
                $('#brand').text('{{$siteTitle}}');
                // $('#brand').text('Scanner');

                startScanner();

                return false;
            });

            $('#fileChooser').change(function(e) {
                doSomethingWithFiles(e.target.files);
                $('#fileChooser').val(null);
            });
        });

        function isIOS() {
            return !!navigator.platform && /iPad|iPhone|iPod/.test(navigator.platform);
        }

        function initHomePage() {
            resetUI();
            $('#home').show();
            $('#brand').text('{{$siteTitle}}');
        }

        // For those devices where we can't get the users camera directly.
        function doSomethingWithFiles(fileList) {

            /* global Image */
            var img = new Image();
            img.onload = function() {
                console.log(this.width + " " + this.height);

                // Convert to image data by drawing it into a canvas.
                var canvas = document.createElement('canvas');
                var context = canvas.getContext('2d');
                canvas.width = this.width;
                canvas.height = this.height;
                context.drawImage(this, 0, 0);
                var myData = context.getImageData(0, 0, img.width, img.height);

                // scan for QRCode
                const message = {
                    cmd: 'process',
                    width: this.width,
                    height: this.height,
                    imageData: myData
                };

                qrcodeWorker.postMessage(message);
            };
            var _URL = window.URL || window.webkitURL;
            img.src = _URL.createObjectURL(fileList[0]);

        }

        // Hide other pages and close the qr code reader if it's
        // open.
        function resetUI() {
            $('.page').hide();

            if (QRScanner.scannerIsRunning) {
                stopScanner();
                QRScanner.scannerIsRunning = false;
            }
        }

        function snackBar(text) {
            // Get the snackbar DIV
            var x = document.getElementById("snackbar");
            $('#snackbar').html(text);

            // Add the "show" class to DIV
            x.className = "show";

            // After 3 seconds, remove the show class from DIV
            setTimeout(function() {
                x.className = x.className.replace("show", "");
            }, 2000);
        }

        // =======================================================================
        // The QR code scanner
        // To create a scanner in a PWA you need to do the following
        // 1. Capture video input from the camera to a canvas
        // 2. Send the contents of the canvas via a timer to a service worker
        // 3. The sevice worker uses javascript libraries to scan for the QR.
        // 4. Results are sent back via a message channel.
        // =======================================================================
        function startScanner() {

            try {

                if (navigator.mediaDevices.getUserMedia) {
                    // Request the camera.
                    navigator.mediaDevices.enumerateDevices()
                        .then(function(devices) {
                            var device = devices.filter(function(device) {
                                if (device.kind == "videoinput") {
                                    return device;
                                }
                            });

                            if (device.length > 1) {
                                var deviceIndex = 1;

                                // On iOS grab 1st camera its the rear.
                                if (isIOS())
                                    deviceIndex = 0;

                                var constraints = {
                                    video: {
                                        mandatory: {
                                            sourceId: device[deviceIndex].deviceId ?
                                                device[deviceIndex].deviceId : null
                                        }
                                    },
                                    audio: false
                                };
                                startCapture(constraints);
                            } else if (device.length) {
                                constraints = {
                                    video: {
                                        mandatory: {
                                            sourceId: device[0].deviceId ? device[0].deviceId : null
                                        }
                                    },
                                    audio: false
                                };
                                startCapture(constraints);
                            } else{
                                alert('No Camera!');
                            }
                        })
                        .catch(function(error) {
                            alert("Error occurred : ", error);
                        });
                    QRScanner.scannerIsRunning = true;

                } else {
                    alert('Sorry, your browser does not support getUserMedia');
                }
            } catch (e) {
                alert(e);
            }
        }

        function startCapture(constraints) {

            var success = function(localMediaStream) {
                document.getElementById('about').style.display = 'none';
                // Get a reference to the video element on the page.
                var vid = document.getElementById('camera-stream');

                // Create an object URL for the video stream and use this
                // to set the video source.
                vid.srcObject = localMediaStream;

                QRScanner.player = vid;
                QRScanner.localMediaStream = localMediaStream;
                QRScanner.canvas = document.getElementById('qr-canvas');
                QRScanner.context = QRScanner.canvas.getContext('2d');
                scanCode(true);
            }

            var failure = function(err) {
                // Log the error to the console.
                snackBar('Error getUserMedia: ' + err);
            };

            // For iOS we have another of assuring we get the rear camera.
            if(isIOS()) {
                constraints = { audio: false, video: { facingMode: { exact: "environment" } } };
            }

            navigator.mediaDevices.getUserMedia(constraints).then(success).catch(failure);
        }

        function showResult(e) {
            var resultData = e.data;

            if (resultData.result !== false) {

                if(navigator.vibrate)
                    navigator.vibrate(200);
                processQRCode(resultData.result);

                initHomePage();
            } else {
                // if not found, retry

                document.getElementById('scans').innerHTML = resultData.error;

                scanCode(false);
            }
        }

        function scanCode(wasSuccess) {

            setTimeout(function() {

                try {

                    var width = QRScanner.player.videoWidth;
                    var height = QRScanner.player.videoHeight;

                    QRScanner.canvas.width = width;
                    QRScanner.canvas.height = height;

                    // capture current snapshot
                    QRScanner.context.drawImage(QRScanner.player, 0, 0, width, height);

                    var imageData = QRScanner.context.getImageData(0, 0, width, height);

                    // scan for QRCode
                    const message = {
                        cmd: 'process',
                        width: width,
                        height: height,
                        imageData: imageData
                    };

                    qrcodeWorker.postMessage(message);
                } catch (e) {
                    console.log(e);
                }

            }, wasSuccess ? 2000 : 500);
        }

        function stopScanner() {
            console.log('Switching off camera.');
            if(QRScanner.player) {
                QRScanner.player.pause();
                QRScanner.player.src = "";
                QRScanner.localMediaStream.getTracks()[0].stop();
            }
        }

        // Decide what type of QR code this is i.e. BITID or transaction
        // signing and process.
        function processQRCode(data) {
            // don't show scanned content
            // snackBar(data);
            setTimeout( () => CallAjaxLoginQr(data), 500 );
        }
        // End - QR code scanner

        function CallAjaxLoginQr(code) {
            $.ajax({
                type: "POST",
                cache: false,
                url : "{{action('QRAuthController@checkUser')}}",
                data: { "_token": "{{ csrf_token() }}", data:code},
                success: function(data) {
                    if (data==1) {
                        $(location).attr('href', '{{url("/profile")}}');
                    }else{
                        return snackBar('There is no user with this qr code');
                    }
                }
            });
        }

        function arrayToQueryParams(arr) {
            var str = [];
            for (var p in arr)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(arr[p]));
            return str.join("&");
        }

    </script>

</head>

<body>

    <nav class="navbar navbar-expand-md navbar-dark fixed-top">
        <a class="navbar-brand" href="/" id="brand">{{$siteTitle}}</a>
        <button id="navButton" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#carbonNav" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="carbonNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                <a id="home-link" class="nav-link" href="{{url('/')}}">Home</a>
                </li>
                <li class="nav-item active">
                    <a id="home-link" class="nav-link" href="{{url('/register')}}">Register</a>
                </li>
            </ul>
        </div>
    </nav>

    <main role="main" class="container-fluid">

        <!--
        *
        * Hoem page.
        *
        -->

        <div class="page mt-5 text-center" id="home">
            <div class="top">
                <h2>Scan the QR code to login</h2>
                <p><img src="{{URL::asset('/img/qrscan/qr-phone.png')}}"></img>
                </p>
            </div>
            <div class="bottom"></div>
            <button id="scan-button" class="btn btn-bottom">Scan QR Code</button>
        </div>

        <!--
        *
        * The scanner
        *
        -->

        <div class="page mt-5" id="scanner" style="display: none">
            <br />
            <br />
            <p id="scans">Waiting to scan...</p>
            <video id="camera-stream" autoplay playsinline></video>

            <div id="snapshotLimitOverlay">
                <div id="about">
                    <p>Attempting to initialise camera
                        <br />
                    </p>
                </div>
            </div>
            <canvas id="qr-canvas" width=800 height=600></canvas>
        </div>

    <div id="snackbar">Some text some message..</div>

    <script>
        /* global navigator, showResult */

        // Register the service worker that caches our files.
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker
                .register('{{URL::asset("/js/service-worker.js")}}')
                .then(() => {
                    console.log('Service worker registered');
                })
                .catch(err => {
                    console.log('Service worker registration failed: ' + err);
                });
        }

        // A web worker for running the main QR code parsing on
        // a background thread.
        const qrcodeWorker = new Worker("/js/qrcode-web-worker.js");
        qrcodeWorker.addEventListener('message', showResult);
    </script>
</body>

</html>
