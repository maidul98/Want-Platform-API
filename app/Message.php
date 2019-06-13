<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Message extends Model
{
    protected $fillable = ['message', 'user_id', 'seen'];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['conversation'];

    /**
     * Each message belongs to one user 
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    /**
     * Each message belongs to a conversation 
     */
    public function conversation(){
        return $this->belongsTo('App\Conversation');
    }

    /**
     * Each message belongs to a conversation 
     */
    public function attachments(){
        return $this->hasMany(Attachment::class, 'message_id');
    }

    public static function userInConvo($id, $user_id){
        DB::table('users')->where('name', 'John')->first();
        if($this->wanter_id == $user_id |$this->fulfiller_id == $user_id){
            return true;
        }
        
        return false;
    }

}
