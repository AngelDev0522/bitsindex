<?php

namespace App\Http\Controllers;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Email;
use MaddHatter\LaravelFullcalendar\Facades\Calendar;

class WelcomeController extends Controller
{
    //compose
    public function index()
    {
        return view('welcome'); //landing page
    }
}
