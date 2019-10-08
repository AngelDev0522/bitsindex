<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artisan;
use Auth;
use DB;
use View;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Carbon\Carbon;
use Session;

class QRAuthController extends Controller
{
    public function clear(Request $request) {
        Artisan::call('cache:clear');
        echo "Cache is cleared <br>";
        Artisan::call('config:clear');
        echo "config Cache is cleared<br>";
        Artisan::call('route:clear');
        echo "route Cache is cleared<br>";
        Artisan::call('view:clear');
        echo "view Cache is cleared<br>";
    }

    public function checkUser(Request $request) {
        if (Session::get('blockTime')) {
            $curTime = Carbon::now();
            $interval = $curTime->diffInSeconds(Session::get('blockTime'));
            if ($interval < 6)
                return json_encode(["result" => 3, "seconds" => 6 - $interval]);
        }

        if ($request->data) {
            $signinCoin = setting('site.signin_coin', 'litecoin').'_secret';

            $user = User::where($signinCoin, $request->data)->first();

            if ($user) {
                if($user->banned){
                    return json_encode(["result" => 2]); //banned
                }
                Auth::login($user);
                return json_encode(["result" => 0]); //success
            }else{
                session(['blockTime' => Carbon::now()]);
                return json_encode(["result" => 1]); //no user
            }
        }
        return 1;
   }

    public function login(Request $request){
        return view('auth.qr-login');
    }

    public function getWalletSecret($wallets, $coin){
        return $wallets[$coin]['data']['secret'];
    }

    public function getWalletAddress($wallets, $coin){
        return $wallets[$coin]['data']['address'];
    }

    public function register(Request $request){
        $imagePath = 'users/dummy_user.png';
        $wallets = [
            'litecoin' => json_decode(file_get_contents('http://localhost:8080/api/v1/litecoin/new'), true),
            'peercoin' => json_decode(file_get_contents('http://localhost:8080/api/v1/peercoin/new'), true),
            'ripple' => json_decode(file_get_contents('http://localhost:8080/api/v1/ripple/new'), true),
        ];
        $dummyUsername=str_random(20);
        $dummyEmail = str_random(10).'@bitsindex.com';
        $dummyPassword = str_random(30);

        $user = User::create([
            'name' => $dummyUsername,
            'email' => $dummyEmail,
            'password' => Hash::make($dummyPassword),
            'avatar' => $imagePath,
            'litecoin_address' => $this->getWalletAddress($wallets, 'litecoin'),
            'litecoin_secret' => $this->getWalletSecret($wallets, 'litecoin'),
            'ripple_address' => $this->getWalletAddress($wallets, 'ripple'),
            'ripple_secret' => $this->getWalletSecret($wallets, 'ripple'),
            'peercoin_address' => $this->getWalletAddress($wallets, 'peercoin'),
            'peercoin_secret' => $this->getWalletSecret($wallets, 'peercoin'),
        ]);

        $authCoin = 'litecoin';
        return view('auth.qr-register', ['qrcontent' => $this->getWalletSecret($wallets, $authCoin)]);
    }

    public function activate(Request $request) {
        return view('auth.qr-activate');
    }
}
