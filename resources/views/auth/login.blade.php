@extends('layouts.auth')


@section('title', 'Login')


@section('content')
<h3 class="login_login">Login</h3>
<!-- <p>Perfectly designed and precisely prepared admin theme with over 50 pages with extra new web app views. -->
<!--Continually expanded and constantly improved Inspinia Admin Them (IN+)-->
<!-- </p> -->
<!-- <p>Login in. To see it in action.</p> -->
<form class="m-t" role="form" method="POST" action="{{ url('/login') }}">
    {{ csrf_field() }}
    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
        <!-- <label for="email" class="control-label">E-Mail Address</label> -->
        <!-- <input type="email" class="form-control" placeholder="E-Mail Address" required=""> -->
        <input id="name" type="text" class="form-control" placeholder="Username" name="name"
            value="{{ old('name') }}" required autofocus>
        @if ($errors->has('name'))
        <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>
    <div class="form-group">
        <!-- <label for="email" class="control-label">Password</label> -->
        <!-- <input type="password" class="form-control" placeholder="Password" required=""> -->
        <input id="password" type="password" placeholder="Password" class="form-control" name="password" required>
        @if ($errors->has('password'))
        <span class="help-block">
            <strong>{{ $errors->first('password') }}</strong>
        </span>
        @endif
    </div>
    <div class="form-group">
        <div class="col-md-6">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember"> Remember Me
                </label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary block full-width m-b">Login</button>

    {{-- <a href="#"><small>Forgot password?</small></a> --}}
    @isset($error)
        <p class="text-muted text-center" style = "color:red;">{{$error}}</p>
    @endisset
    <p class="text-muted text-center"><small>Login using another method?</small></p>
    <a class="btn btn-sm btn-white btn-block" href="{{ url('/qr-login') }}">Login by QR Code</a>
    <p class="text-muted text-center"><small>Do not have an account?</small></p>
    <a class="btn btn-sm btn-white btn-block" href="{{ url('/qr-activate') }}">Create an account</a>
</form>
<!-- <p class="m-t"> <small>Inspinia we app framework base on Bootstrap 3 &copy; 2014</small> </p> -->
@endsection
