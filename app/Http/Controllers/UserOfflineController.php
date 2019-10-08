<?php

namespace App\Http\Controllers;

use App\Events\UserOffline;
use App\User;
use Illuminate\Http\Request;

class UserOfflineController extends Controller
{
    public function __invoke(User $user)
    {
        $user->online = '0';
        $user->save();

        broadcast(new UserOffline($user));
    }
}
