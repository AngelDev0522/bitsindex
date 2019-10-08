<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Helpers\GoogleAuthenticator;

class User extends \TCG\Voyager\Models\User
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','avatar',
        'litecoin_address', 'litecoin_secret',
        'peercoin_address', 'peercoin_secret',
        'ripple_address', 'ripple_secret',
        'google_auth_code', 'enable_2_auth',
        'profile_visible','activated',
        'banned','online',
        'enable_chat','enable_email',
        'enable_calendar','enable_wallet'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function messages() {
        return $this->hasMany('App\Message');
    }
    public function emails() {
        return $this->hasMany('App\Email');
    }

    public function set_random_2fa_secret() {
        $GA = new GoogleAuthenticator();
        $secret = $GA->createSecret();

        $this->google_auth_code = $secret;
        $this->update();
        return $secret;
    }
}
