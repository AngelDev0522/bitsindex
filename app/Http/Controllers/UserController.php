<?php

namespace App\Http\Controllers;
use App\Events\MessagePosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests;
use App\User;
use App\Role;
use Validator;
use Session;
use Auth;
use Route;
use Activation;
use DB;
use Hash;
use Mail;
use Carbon\Carbon;
use App\Helpers\GoogleAuthenticator;


class UserController extends Controller
{
    public function __construct()
    {
      //$this->middleware('auth')->except('orders');
      // $this->middleware('auth');
    }
    protected function validator(Request $request,$id='')
    {
        // return Validator::make($request->all(), [
        //     'first_name' => 'required|min:2|max:35|string',
        //     'last_name' => 'required|min:2|max:35|string',
        //     'username' => Sentinel::inRole('Admin')?'required|min:3|max:50|string':(Sentinel::check()?'required|min:3|max:50|string|unique:users,username,'.$id:'required|min:3|max:50|unique:users|string'),
        //     'password' => 'min:6|max:50|confirmed',
        //     //'gender' => 'required',
        //     'role' => 'required',
        // ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->enable_chat == 1)
            return view('/chat'); //FIXME: to profile
        else
            return view('/profile');
    }

    public function getOtherUsers(Request $request){
        //FIXME: only provide essential data
        $user = Auth::user();
        $users = DB::table('users')
                ->select('id', 'name', 'avatar', 'online')
                ->where('users.id','!=',$user->id)
                ->where('users.role_id','!=','1')   //not admin
                ->where('users.profile_visible','1')   //set profile to visible
                ->where('users.activated','1')   //set profile to visible
                ->where('users.banned','0')   //not banned
                ->where('users.enable_chat','1')   //chat enabled
                ->get();
        return $users;
   }

    public function getStoredMessage($userId, Request $request)
    {
        $user = Auth::user();
        $output = DB::table('messages')
            ->leftJoin('users','users.id','=','messages.user_id')
            ->join('receivers','receivers.message_id','=','messages.id')
            ->where('messages.user_id','=',$user->id)
            ->where('receivers.user_id','=',$userId)
            ->orWhere('messages.user_id','=',$userId)
            ->where('receivers.user_id','=',$user->id)
            ->select('users.name as user','users.avatar','users.id as userId','messages.message','messages.file_path','messages.file_name','messages.type','messages.created_at as time','receivers.user_id as r_user_id')
            ->orderBy("messages.id","asc")
            ->get();
        return $output;
    }

    public function postNewMessage($userId, Request $request)
    {
        $user = Auth::user();
        $message = $user->messages()->create([
            'message'=>request()->get('message'),
            'type'=>request()->get('type'),
        ]);

        $message->receivers()->create([
            'user_id'=>$userId
        ]);
        broadcast(new MessagePosted($message,$user,$userId))->toOthers();
        $output['message'] = $message;
        $output['user'] = $user;
        return ['output'=> $output];
    }

    public function postImage($userId, Request $request)
    {
        $file = $request->file;
        $user = Auth::user();
        if (!empty($file)) {
            $fileName = $file->getClientOriginalName();
            // file with path
            $filePath = url('uploads/chats/'.$fileName);
            //Move Uploaded File
            $destinationPath = 'uploads/chats';
            if($file->move($destinationPath,$fileName)) {
                $request['file_path'] = $filePath;
                $request['file_name'] = $fileName;
                $request['message'] = 'file';
                $request['type'] = request('type');
            }
            $message = $user->messages()->create([
                'file_path'=>$request['file_path'],
                'file_name'=>$request['file_name'],
                'message'=>$request['message'],
                'type'=>$request['type'],
            ]);
            // $message = $user->messages()->create($request);

            $message->receivers()->create([
                    'user_id'=>$userId
                ]);

            $output = [];
            broadcast(new MessagePosted($message,$user,$userId))->toOthers();

            $output['message'] = $message;
            $output['user'] = $user;
            return ['output'=> $output];

        }
    }
    public function showEmail(Request $request){
        return View('sendemail');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){
        $roles = Role::get()->pluck('name', 'id');
        return View('backEnd.users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        if ($this->validator($request,Sentinel::getUser()->id)->fails()) {

                return redirect()->back()
                        ->withErrors($this->validator($request))
                        ->withInput();
        }
         //create user
         $user = Sentinel::register($request->all());
         //activate user
         $activation = Activation::create($user);
         $activation = Activation::complete($user, $activation->code);
         //add role
         $user->roles()->sync([$request->role]);

        Session::flash('message', 'Success! User is created successfully.');
        Session::flash('status', 'success');

        return redirect()->route('user.index');
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
         $user = User::findOrFail($id);
         $type = $user->roles()->first();
         if ($request->is('api/*')) {
            $user= User::where('id',$id)->with('activations','roles')->get();
            return response()->json(compact('user'));
        }
        return View('backEnd.users.show', compact('user','type'));
    }
    public function accountFrontEnd(Request $request,$id)
    {
        $user=Sentinel::getUser();
         if ($user->inRole('admin')) {
           $user = User::findOrFail($id);
           return view('frontend.userAcount',compact('user'));
         }

        return view('frontend.userAcount',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $user = User::find($id);
        $roles = Role::get()->pluck('name', 'id');
        return View('backEnd.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $update_user = Validator::make($request->all(), [
            'first_name' => 'min:2|max:35|string',
            'last_name' => 'min:2|max:35|string',
            'username' => Sentinel::inRole('Admin')?'required|min:3|max:50|string':(Sentinel::check()?'required|min:3|max:50|string|unique:users,username,'.$id:'required|min:3|max:50|unique:users|string'),
        ]);

        if ($update_user->fails()) {
            return redirect()->back()
                        ->withErrors($update_user)
                        ->withInput();
        }

        $user = User::find($id);
        if ($user) {

              if($request->first_name){
              $user->first_name=$request->first_name;
              }
              if($request->last_name){
              $user->last_name=$request->last_name;
              }
              if($request->username){
              $user->username=$request->username;
              }
              if($request->new_password && $request->new_password_confirmation ){
                if ($request->new_password == $request->new_password_confirmation ){
                     $user->password=bcrypt($request->new_password);
                 }else{
                   Session::flash('message', 'Your old password is incorrect.');
                   Session::flash('status', 'error');
                  return redirect()->back()->withErrors(['old_password', 'your old password is incorrect']);
                 }
              }
              $user->update();
            if ($request->role) {
              $user->roles()->sync([$request->role]);
            }
            Session::flash('message', 'Success! User is updated successfully.');
            Session::flash('status', 'success');

        }


      return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        Session::flash('message', 'Success! User is deleted successfully.');
        Session::flash('status', 'success');

        return redirect()->route('user.index');
    }

    public function permissions($id)
    {
        $user = Sentinel::findById($id);
        $routes = Route::getRoutes();


        //Api Route
        // $api = app('api.router');
        // /** @var $api \Dingo\Api\Routing\Router */
        // $routeCollector = $api->getRoutes(config('api.version'));
        // /** @var $routeCollector \FastRoute\RouteCollector */
        // $api_route = $routeCollector->getRoutes();


        $actions = [];
        foreach ($routes as $route) {
            if ($route->getName() != "" && !substr_count($route->getName(), 'payment')) {
                $actions[] = $route->getName();
            }
        }

        //remove store option
        $input = preg_quote("store", '~');
        $var = preg_grep('~' . $input . '~', $actions);
        $actions = array_values(array_diff($actions, $var));

        //remove update option
        $input = preg_quote("update", '~');
        $var = preg_grep('~' . $input . '~', $actions);
        $actions = array_values(array_diff($actions, $var));

        //Api all names
        // foreach ($api_route as $route) {
        //     if ($route->getName() != "" && !substr_count($route->getName(), 'payment')) {
        //         $actions[] = $route->getName();
        //     }
        // }

        $var = [];
        $i = 0;
        foreach ($actions as $action) {

            $input = preg_quote(explode('.', $action )[0].".", '~');
            $var[$i] = preg_grep('~' . $input . '~', $actions);
            $actions = array_values(array_diff($actions, $var[$i]));
            $i += 1;
        }

        $actions = array_filter($var);
        // dd (array_filter($actions));

        return View('backEnd.users.permissions', compact('user', 'actions'));
    }

    public function save($id, Request $request)
    {
        //return $request->permissions;
        $user = Sentinel::findById($id);
        $user->permissions = [];
        if($request->permissions){
            foreach ($request->permissions as $permission) {
                if(explode('.', $permission)[1] == 'create'){
                    $user->addPermission($permission);
                    $user->addPermission(explode('.', $permission)[0].".store");
                }
                else if(explode('.', $permission)[1] == 'edit'){
                    $user->addPermission($permission);
                    $user->addPermission(explode('.', $permission)[0].".update");
                }
                else{
                    $user->addPermission($permission);
                }
            }
        }

        $user->save();

        Session::flash('message', 'Success! Permissions are stored successfully.');
        Session::flash('status', 'success');

        return redirect()->route('user.index');
    }

    public function activate(Request $request,$id)
    {
        $user = Sentinel::findById($id);

        $activation = Activation::completed($user);

        if($activation){
            Session::flash('message', 'Warning! The user is already activated.');
            Session::flash('status', 'warning');

            return redirect('user');
        }
        $activation = Activation::create($user);
        $activation = Activation::complete($user, $activation->code);

        Session::flash('message', 'Success! The user is activated successfully.');
        Session::flash('status', 'success');

        $role = $user->roles()->first()->name;

        return redirect()->route('user.index');
    }

    public function deactivate(Request $request,$id){

        $user = Sentinel::findById($id);
        Activation::remove($user);

        Session::flash('message', 'Success! The user is deactivated successfully.');
        Session::flash('status', 'success');

        return redirect()->route('user.index');
    }
    public function ajax_all(Request $request){
        if ($request->action=='delete') {
           foreach ($request->all_id as $id) {
             $user = User::findOrFail($id);
             if ($user->deleted_at == null){$user->delete();}
            }
            Session::flash('message', 'Success! Users are deleted successfully.');
            Session::flash('status', 'success');
            return response()->json(['success' => true, 'status' => 'Sucesfully Deleted']);
        }
        if ($request->action=='deactivate') {
           foreach ($request->all_id as $id) {
             $user = User::findOrFail($id);
             $activation = Activation::completed($user);
             if ($activation){Activation::remove($user);}
            }
            Session::flash('message', 'Success! Users are deactivate successfully.');
            Session::flash('status', 'success');
            return response()->json(['success' => true, 'status' => 'Sucesfully deactivate']);
        }
        if ($request->action=='activate') {
           foreach ($request->all_id as $id) {
             $user = User::findOrFail($id);
             $activation = Activation::completed($user);
             if ($activation==''){
                $activation = Activation::create($user);
                $activation = Activation::complete($user, $activation->code);
                }
            }
            Session::flash('message', 'Success! Users are Activated successfully.');
            Session::flash('status', 'success');
            return response()->json(['success' => true, 'status' => 'Sucesfully Activated']);
        }
    }

    public function get2FACode(Request $request) {
        $GA = new GoogleAuthenticator();

		Auth::user()->google_auth_code = trim(Auth::user()->google_auth_code);

		if(!(strlen(Auth::user()->google_auth_code) > 6)){
            Auth::user()->google_auth_code = Auth::user()->set_random_2fa_secret();
            Auth::login(Auth::user());
        }

		echo json_encode([
            'qrCodeUrl' => $GA->getQRCodeGoogleUrl(Auth::user()->email, Auth::user()->google_auth_code, 'https://bitsindex.com'),
            'secret' => Auth::user()->google_auth_code,
        ]);
    }

    public function verify2FACode(Request $request) {
		$GA = new GoogleAuthenticator();
		$verifyCode = $request->verifyCode;
		$checkResult = $GA->verifyCode(Auth::user()->google_auth_code, $verifyCode, 1);
		if($checkResult){
            if (Auth::user()->enable_2_auth == 0) {
                //Enable
                Auth::user()->enable_2_auth = 1; //device confirmed
                Auth::user()->update();
                Auth::login(Auth::user());

                Session::flash('message', "Enabled 2 Factor Authentication successfully !" );
                Session::flash('type', 'success');
                session(['verify2FA' => true]);
                echo json_encode([
                    'result' => true
                ]);
                return;
            } else {
                //Disable
                Auth::user()->enable_2_auth = 0; //device confirmed
                Auth::user()->update();
                Auth::login(Auth::user());

                Session::flash('message', "Disabled 2 Factor Authentication successfully !" );
                Session::flash('type', 'success');
                echo json_encode([
                    'result' => true
                ]);
                return;
            }
        }
        echo json_encode([
            'result' => false
        ]);
    }

    public function twoFALogin() {
        if (!Auth::user()->enable_2_auth || Session::get("verify2FA"))
            return redirect('/profile');
        return view('auth.2fa');
    }

    public function twoFAVerify(Request $request) {
		$GA = new GoogleAuthenticator();
		$verifyCode = $request->verifyCode;
        $checkResult = $GA->verifyCode(Auth::user()->google_auth_code, $verifyCode, 1);

		if ($checkResult) {
            Session::flash('message', "Welcome !" );
            Session::flash('type', 'success');
            session(['verify2FA' => true]);
            if(Auth::user()->role_id == 1)
                return redirect('/admin');
            else
                return redirect('/profile');
        } else {
            Session::flash('message', "2 Factor Authentication failed !" );
            Session::flash('type', 'warning');
            return redirect('/2fa-login');
        }
    }
}
