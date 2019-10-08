<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/profile';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'alpha_dash', 'unique:users', 'max:255'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    public function register(Request $request)
    {
        $validation = $this->validator($request->all());
        if($validation->fails()){
            return redirect()->back()
                    ->withErrors($validation)
                    ->withInput();
        }
        $imagePath = 'users/dummy_user.png';
        $file = $request->file('image');
        if ($file !== null) {
            $ext = $file->extension();

            // $filename = str_random(20);
            // $file = $request->file('image')->storeAs('users', $filename.'.'.$ext);

            $imageName = str_random(20).'.'.$ext;
            $destinationPath = 'storage/users/';
            if($file->move($destinationPath,$imageName)) {
                $imagePath = 'users/'.$imageName;
            }
        }
        $litecoin = json_decode(file_get_contents('http://localhost:8080/api/v1/litecoin/new'), true);
        $peercoin = json_decode(file_get_contents('http://localhost:8080/api/v1/peercoin/new'), true);
        $ripple = json_decode(file_get_contents('http://localhost:8080/api/v1/ripple/new'), true);

        User::create([
            'name' => request()->get('name'),
            'email' => str_random(10).'@bitsindex.com', //request()->get('email'),
            'password' => Hash::make(request()->get('password')),
            'avatar' => $imagePath,
            'litecoin_address' => $litecoin['data']['address'],
            'litecoin_secret' => $litecoin['data']['secret'],
            'ripple_address' => $ripple['data']['address'],
            'ripple_secret' => $ripple['data']['secret'],
            'peercoin_address' => $peercoin['data']['address'],
            'peercoin_secret' => $peercoin['data']['secret'],
        ]);
        $error['error'] = "Successfully Registered";
        return view('auth.login')->with($error);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'avatar' => "user/".$data['image'],
        ]);
    }
}
