@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<h3 class="login_login">Register</h3>
<!-- <p>Create account to see it in action.</p> -->
<form class="m-t" role="form" enctype="multipart/form-data" method="POST" action="{{ url('/register') }}">
    {{ csrf_field() }}
    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        <!-- <input type="text" class="form-control" placeholder="Name" required=""> -->
        <input id="name" type="text" class="form-control" placeholder="Username" name="name" value="{{ old('name') }}"
            required autofocus>
        @if ($errors->has('name'))
        <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>
    {{-- <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
        <!-- <input type="email" class="form-control" placeholder="Email" required=""> -->
        <input id="email" type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}"
            required>

        @if ($errors->has('email'))
        <span class="help-block">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
        @endif
    </div> --}}
    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
        <!-- <input type="password" class="form-control" placeholder="Password" required=""> -->
        <input id="password" type="password" class="form-control" placeholder="Password" name="password" required>

        @if ($errors->has('password'))
        <span class="help-block">
            <strong>{{ $errors->first('password') }}</strong>
        </span>
        @endif
    </div>
    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
        <!-- <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label> -->

        <input id="password-confirm" type="password" class="form-control" placeholder="Password Confirmation"
            name="password_confirmation" required>
    </div>
    <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
        <label for="confirm" class="cols-sm-2 control-label">Avatar</label>
        <div class="cols-sm-10">
            <div class="input-group">
                <input type="file" class="form-control" name="image">
            </div>
        </div>
        @if ($errors->has('image'))
        <span class="help-block">
            <strong>{{ $errors->first('image') }}</strong>
        </span>
        @endif
    </div>
    <!-- <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">">
                    <div class="checkbox i-checks"><label> <input type="checkbox"><i></i> Agree the terms and policy </label></div>
                </div> -->
    <button type="submit" class="btn btn-primary block full-width m-b">Register</button>

    {{-- <p class="text-muted text-center"><small>Register using another method?</small></p>
    <a class="btn btn-sm btn-white btn-block" href="{{ url('/qr-register') }}">Register by QR Code</a> --}}
    <p class="text-muted text-center"><small>Already have an account?</small></p>
    <a class="btn btn-sm btn-white btn-block" href="{{ url('/login') }}">Login</a>
</form>
<!-- <p class="m-t"> <small>Inspinia we app framework base on Bootstrap 3 &copy; 2014</small> </p> -->
@endsection

@section('custom_js')
<script src="{{ URL::asset('/Inspina/js/plugins/iCheck/icheck.min.js ') }}"></script>
<!-- iCheck -->
<script>
    $(document).ready(function(){
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    });
</script>
@endsection
