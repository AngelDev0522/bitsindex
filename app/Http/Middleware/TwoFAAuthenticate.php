<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Session;

class TwoFAAuthenticate
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param  string|null  $guard
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    if(!Auth::check())
      return redirect('/admin/login');
    if (!Auth::user()->enable_2_auth || Session::get("verify2FA"))
      return $next($request);
    else {
      return redirect('/2fa-login');
    }
  }
}
