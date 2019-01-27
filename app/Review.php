<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Review extends Model
{
    /**
     * A user has reviews(s)
     */
    public function user(){
        
        return $this->belongsTo('App\User');
    }

    public function fulfiller(){
        return $this->belongsTo(User::class, 'fulfiller_id');
    }

    public function wanter(){
        return $this->belongsTo(User::class, 'want_id');
    }




}
