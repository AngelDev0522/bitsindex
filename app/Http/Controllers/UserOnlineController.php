<?php

namespace App\Http\Controllers;

use App\User;
use App\Events\UserOnline;
use Illuminate\Http\Request;

class UserOnlineController extends Controller
{
    public function __invoke(User $user)
    {
        $user->online = '1';
        $user->save();

        broadcast(new UserOnline($user));
    }
}
