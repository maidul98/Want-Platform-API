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

    /**
     * Each review has a fulfiller
     */
    public function fulfiller(){
        return $this->belongsTo(User::class, 'fulfiller_id');
    }

    /**
     * Each review has a wanter 
     */
    public function wanter(){
        return $this->belongsTo(User::class, 'want_id');
    }

    /**
     * Get the want this review is about 
     */
    public function want(){
        return $this->belongsTo(Want::class, 'want_id');
    }




}
