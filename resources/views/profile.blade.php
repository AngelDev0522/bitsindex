@extends('layouts.app')

@section('content')

<div class="p-w-md m-t-sm">
    <div class="row">{{--  middle-box --}}
        <div class="col-md-6">
            <h3 class="login_login">Update Profile</h3>
            <!-- <p>Create account to see it in action.</p> -->
            <form class="m-t" role="form" enctype = "multipart/form-data" method="POST" action="{{ url('/updateprofile') }}">
            {{ csrf_field() }}
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <input id="name" type="text" class="form-control" placeholder="Name" name="name" value="{{ $user->name }}" required autofocus>
                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input id="email" type="email" class="form-control" placeholder="Email" name="email" value="{{ $user->email }}" required>

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <!-- <input type="password" class="form-control" placeholder="Password" required=""> -->
                    <input id="password" type="password" class="form-control" placeholder="Password" name="password">

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <!-- <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label> -->

                    <input id="password-confirm" type="password" class="form-control" placeholder="Password Confirmation" name="password_confirmation">
                </div>
                <div class="form-group{{ $errors->has('avatar') ? ' has-error' : '' }}">
                    <label for="confirm" class="cols-sm-2 control-label">Avatar</label>
                    <div class="cols-sm-10">
                        <div class="input-group">
                        <input  type="file" class="form-control" name="avatar">
                        </div>
                    </div>
                    @if ($errors->has('avatar'))
                        <span class="help-block">
                            <strong>{{ $errors->first('avatar') }}</strong>
                        </span>
                    @endif
                </div>
                {{-- <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">">
                    <div class="checkbox i-checks"><label> <input type="checkbox"><i></i> Agree the terms and policy </label></div>
                </div> --}}

                <div class="form-group{{ $errors->has('profile_visible') ? ' has-error' : '' }}">
                    <label class="">
                        <input class="i-checks" type="checkbox" name="profile_visible" value={{$user->profile_visible}} {{$user->profile_visible ? 'checked' : ''}}>
                        Profile Visibility
                    </label>
                    @if ($errors->has('profile_visible'))
                        <span class="help-block">
                            <strong>{{ $errors->first('profile_visible') }}</strong>
                        </span>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary block full-width m-b">Update</button>

                <!-- <p class="text-muted text-center"><small>Already have an account?</small></p> -->
                <!-- <a class="btn btn-sm btn-white btn-block" href="{{ url('/login') }}">Login</a> -->
            </form>
        </div>
        <div class="col-md-6">
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
    </div>

    <hr/>

    <div class="row">
        <div class="col-md-6">
            <h4 class="login_login">2 Factor Authentication</h4>
            <ul>
                <li>
                    Install Google Authenticator application to your mobile phone<br><br>
                    <div class="row">
						<div class="col-md-6 text-right">
							<a class="appbadge" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&amp;hl=en" target="_blank">
								<img height="45" src="http://www.niftybuttons.com/googleplay/googleplay-button8.png" alt="Get on Google Play">
							</a>
						</div>
						<div class="col-md-6 text-left">
							<a class="appbadge" href="https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8?at=1000lc66" target="_blank">
								<img height="45" src="http://www.niftybuttons.com/itunes/itunesbutton1.png" alt="iTunes Button">
							</a>
						</div>
					</div><br>
                </li>
                <li>Scan QR code.</li>
                <li>Submit 2FA code.</li>
            </ul>

            @if (!Auth::user()->enable_2_auth)
            <button class="btn btn-primary full-width" onclick="onEnable()">Enable</button>
            @else
            <button class="btn btn-warning full-width" onclick="onDisable()">Disable</button>
            @endif
            <br><br>
        </div>
    </div>

</div>
@endsection

@include('profile.2fa')

@section('scripts')
<script>
$(function() {

});

const onEnable = () => {
    load2FaInfor();
}
const onDisable = () => {
    load2FaInfor();
}
</script>
@endsection
