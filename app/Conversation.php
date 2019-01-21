<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'wanter_id', 'fulfiller_id', 'updated_at'
    ];

    /**
     * Each conversation has many messages
     */
    public function messages(){
        return $this->hasMany('App\Message');
    }

    /**
     * Each conversation has one wanter
     */
    public function wanter(){
        return $this->belongsTo(User::class, 'wanter_id');
    }

    /**
     * Each conversation has one fulfiller 
     */
    public function fulfiller(){
        return $this->belongsTo(User::class, 'fulfiller_id');
    }

    /**
     * Each conversation has one fulfiller 
     */
    public function attachment(){
        return $this->belongsTo(Attachment::class, 'message_id');
    }
    

    
}
