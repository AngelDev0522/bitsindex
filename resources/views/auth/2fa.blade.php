@extends('layouts.auth')


@section('title', 'Login')


@section('content')
<div class="text-center">
  <h3 class="login_login">2 Factor Authentication</h3>

  <form class="form-horizontal" role="form" method="POST" action="{{ url('/2fa-verify') }}">
    {{ csrf_field() }}
    <h2 class="text-center">2FA Code</h4>
    <div class="input-group">
      <span class="input-group-addon"><i class="fa fa-mobile font-size-20"></i></span>
      <input name="verifyCode" min="0" max="999999" step="1" class="form-control border-right-0" type="number" placeholder="6 digit number" autocomplete="off" required>
    </div>
    <br>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</div>
@endsection
