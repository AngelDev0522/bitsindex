<?php

namespace App\Http\Controllers;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Email;
use MaddHatter\LaravelFullcalendar\Facades\Calendar;
use Mail;

class MailController extends Controller
{
    private function getInboxEmails($user){
        $emails = DB::table('emails')
            ->leftJoin('users','users.id','=','emails.user_id')
            ->where('emails.receiver','=',$user->email)
            // ->where('emails.receiver','=',$user->name)
            ->select('emails.id','emails.status','users.email as email','emails.subject','emails.created_at')
            ->get();
        return $emails;
    }

    private function getOutboxEmails($user){
        $emails = DB::table('emails')
            ->leftJoin('users','users.id','=','emails.user_id')
            ->where('emails.user_id','=',$user->id)
            ->select('emails.id','emails.status','emails.receiver','users.email as email','emails.subject','emails.created_at')
            ->get();
        return $emails;
    }

    private function getOneEmail($id){
        $emails = DB::table('emails')
            ->leftJoin('users','users.id','=','emails.user_id')
            ->where('emails.id','=',$id)
            ->select('emails.id','emails.user_id','emails.status','emails.email as content','emails.receiver','users.email as email','emails.subject','emails.created_at')
            ->get();
        if(count($emails))   return $emails[0];
        return null;
    }

    //compose
    public function inbox()
    {
        $user = Auth::user();
        $inbox = $this->getInboxEmails($user);
        $outbox = $this->getOutboxEmails($user);
        return view('inbox',compact('inbox', 'outbox'));
    }
    public function sentmails()
    {
        return redirect('/sentmail')->with(['message' => "Successfully sent",'type' => "success"]);;
    }
    public function sentmail()
    {
        $user = Auth::user();
        $inbox = $this->getInboxEmails($user);
        $outbox = $this->getOutboxEmails($user);
        if(Auth::user()->enable_email == 1)
            return view('sentmail',compact('inbox', 'outbox'));
        else
            return view('home');
    }

    public function compose()
    {
        $user = Auth::user();
        $inbox = $this->getInboxEmails($user);
        $outbox = $this->getOutboxEmails($user);
        if(Auth::user()->enable_email == 1)
            return view('compose',compact('inbox', 'outbox'));
        else
            return view('home');
    }
    public function showemail($id, Request $request)
    {
        $user = Auth::user();
        $email = $this->getOneEmail($id);

        if($email && ($email->receiver == $user->email || $email->user_id == $user->id)){
            $inbox = $this->getInboxEmails($user);
            $outbox = $this->getOutboxEmails($user);
            if(Auth::user()->enable_email == 1)
                return view('showemail',compact('email', 'inbox', 'outbox'));
            else
                return view('home');
        }
        abort(403);
    }

    public function removemail($id, Request $request)
    {
        $deletedRows = Email::where('emails.id', $id)->delete();
        $user = Auth::user();
        $inbox = $this->getInboxEmails($user);
        $outbox = $this->getInboxEmails($user);
        if(Auth::user()->enable_email == 1)
            return redirect('inbox')->with(['message' => "Successfully Removed",'type' => "success"]);
            // return view('inbox',compact('email', 'inbox', 'outbox'));
        else
            return view('home');
    }
    public function sendemail(Request $request)
    {
        $receiver = $request->receiver;
        $value = explode(",",$receiver);
        $email = trim($request->email);
        if(empty($email))   $email = '';
        $subject = trim($request->subject);
        if(empty($subject))   $subject = '';
        $user = Auth::user();
        for($i=0;$i<count($value);$i++)
        {
            if (strpos($value[$i], '@bitsindex.com') !== false) {
                // $name = str_replace('@bitsindex.com', '', $value[$i]);
                try{
                    $user->emails()->create([
                        'user_id'=>$user->id,
                        // 'receiver'=>$name,
                        'receiver'=>trim($value[$i]),
                        'subject'=>$subject,
                        'email'=>$email,
                    ]);
                }catch(Exception $e){
                }
                \array_splice($value,$i,1);
                $i--;
            }
        }
        if(count($value)){
            // $mymail = $user->name.'@bitsindex.com';
            $mymail = $user->email;
            $myname = $user->name;
            $to_name = 'bitsindex';
            $data = array("body" => $email);
            Mail::send('emails.email', $data, function($message) use ($myname,$subject,$to_name,$value,$mymail) {
                $message->to($value, $to_name)
                        ->subject($subject);
                $message->from($mymail,$mymail);
            });
            for($i=0;$i<count($value);$i++)
            {
                $user->emails()->create([
                    'user_id'=>$user->id,
                    'receiver'=>$value[$i],
                    'subject'=>$subject,
                    'email'=>$email,
                ]);
            }
        }
        // var_dump($value);
        // exit(0);
        $data['receiver'] = $receiver;
        $data['subject'] = $subject;
        $data['email'] = $email;
        $data['success'] = 'success';
        return $data;
    }
}
