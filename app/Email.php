<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
class Email extends Model
{
	protected $fillable = ['user_id','email','receiver','subject','status'];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public static function getNotification() {
        $user = Auth::user();
        $emails = DB::table('emails')
            ->leftJoin('users','users.id','=','emails.user_id')
            ->where('emails.receiver','=',$user->name)
            ->where('emails.status','=','0')
            ->select('emails.id','emails.status','users.email as email','emails.subject','emails.created_at')
            ->get();
        return count($emails);
    }
}
