<?php

namespace App\Http\Controllers;
use Auth;
use DB;
use Session;
use View;
use \App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function profile(Request $request){
        if (Auth::user()->role_id == 1)
            return redirect('/');
        $user = Auth::user();
        // Voyager::setting('signin-coin', 'default-value');
        $signinCoin = setting('site.signin_coin', 'litecoin').'_secret';
        $qrcontent = $user->$signinCoin;
        $hard_reload = $request->has('reload');
        if(!$user->activated){
            $user->activated = true;
            $user->update();
            Session::flash('message', 'Your account has been activated! \nYou should keep your keys safe!');
            Session::flash('type', 'success');
        }
        return View('profile', compact('user', 'qrcontent', 'hard_reload'));
    }

    public function welcome()
    {
        return View('welcome');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'alpha_dash', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image'],
            'profile_visible' => ['nullable', 'boolean'],
        ]);
    }

    public function updateprofile(Request $request)
    {
        $userInfo = Auth::user();

        $validation = $this->validator($request->all());
        if($validation->fails()){
            return redirect()->back()
                    ->withErrors($validation)
                    ->withInput();
        }

        $userInfo->name = $request->name;
        $userInfo->email = $request->email;
        $userInfo->profile_visible = $request->has('profile_visible'); //trick https://stackoverflow.com/a/48800830/9234721

        //profile_visible
        // if ($profile_visible = $request->has('profile_visible')) { //checked so true
        //     $updatables['profile_visible'] = true;
        // }

        //password
        if ($password = $request->password) {
            $userInfo->password = Hash::make($password);
        }

        //avatar
        if ($file = $request->file('avatar')) {
            $imagePath = 'users/dummy_user.png';
            $ext = $file->extension();

            // $filename = str_random(20);
            // $file = $request->file('image')->storeAs('users', $filename.'.'.$ext);
            $oldPath = $userInfo->avatar;

            if($imagePath != $oldPath && !empty($oldPath)){
                // existing not default image
                $imageName = str_replace('users/', '', $oldPath);
            } else{
                $imageName = str_random(20).'.'.$ext;
            }
            $destinationPath = 'storage/users/';
            if($file->move($destinationPath,$imageName)) {
                $imagePath = 'users/'.$imageName;
            }
            $userInfo->avatar = $imagePath;
        }

        $userInfo->save();
        return redirect('/profile?reload=1')->with(['message' => "Successfully Updated",'type' => "success"]); //hard reload
    }
}
