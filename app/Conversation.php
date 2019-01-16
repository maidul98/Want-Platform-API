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
     * Each conversation has many messages
     */
    public function wanter(){
        return $this->belongsTo(User::class, 'wanter_id');
    }

    /**
     * Each conversation has many messages
     */
    public function fulfiller(){
        return $this->belongsTo(User::class, 'fulfiller_id');
    }

    
}
