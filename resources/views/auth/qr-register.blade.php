@extends('layouts.auth')

@section('title', 'QR Register')

@section('content')
<h3 class="login_login">QR - Register</h3>
<!-- <p>Perfectly designed and precisely prepared admin theme with over 50 pages with extra new web app views. -->
<!--Continually expanded and constantly improved Inspinia Admin Them (IN+)-->
<!-- </p> -->
<!-- <p>Login in. To see it in action.</p> -->
<div class="card">

    <div class="text-center">
        <img class="" id="qrcode"
            src="data:image/jpg;base64, {!! base64_encode(QrCode::format('png')->color(38, 38, 38, 0.85)->backgroundColor(255, 255, 255, 0.82)->size(300)->generate($qrcontent)) !!}">
        <p>This is your QR code. Download it into your mobile.</p>
        <a class="flex-center" download="qrcode_login.jpg"
            href="data:image/jpg;base64, {!! base64_encode(QrCode::format('png')->color(38, 38, 38, 0.85)->backgroundColor(255, 255, 255, 0.82)->size(300)->generate($qrcontent)) !!}">
            <button id="download_qr" class="btn btn-primary sub6">Download</button>
        </a>
    </div>
</div>
<!-- <p class="m-t"> <small>Inspinia we app framework base on Bootstrap 3 &copy; 2014</small> </p> -->
@endsection
