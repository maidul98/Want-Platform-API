<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['message', 'user_id'];
    
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

}
