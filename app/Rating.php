<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    /**
     * Get the user that owns this rating
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
