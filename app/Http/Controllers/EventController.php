<?php

namespace App\Http\Controllers;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Event;
use MaddHatter\LaravelFullcalendar\Facades\Calendar;

class EventController extends Controller
{
    //
    public function createEvent()
    {
        if(Auth::user()->enable_calendar == 1)
            return view('createevent');
        else
            return view('home');
    }
    public function store(Request $request)
    {
        $event= new Event();
        $event->title=$request->get('title');

        $event->user_id = Auth::user()->id;
        $event->start_date=$request->get('startdate');
        $event->end_date=$request->get('enddate');
        $event->save();
        return redirect('event')->with('success', 'Task has been added');
    }
    public function calender()
    {
        $events = [];
        $data = Event::all();
        if($data->count())
            {
            foreach ($data as $key => $value)
            {
                $events[] = Calendar::event(
                    $value->title,
                    true,
                    new \DateTime($value->start_date),
                    new \DateTime($value->end_date.'+1 day'),
                    null,
                    // Add color
                    [
                        'color' => '#000000',
                        'textColor' => '#008000',
                    ]
                );
            }
        }
        $calendar = Calendar::addEvents($events);
        if(Auth::user()->enable_calendar == 1)
            return view('calendar', compact('calendar'));
        else
            return view('home');
    }
    public function removeevent()
    {
        $user = Auth::user();
        $events = DB::table('events')
            ->where('events.user_id','=',$user->id)
            ->get();
        if(Auth::user()->enable_calendar == 1)
            return view('removeevent',compact('events'));
        else
            return view('home');
    }
    public function remove($id, Request $request)
    {
        $events = DB::table('events')
            ->where('events.id','=',$id)
            ->delete();
        return redirect('/event/remove')->with(['message' => "Successfully Removed",'type' => "success"]);
    }
}
