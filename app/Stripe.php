<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stripe extends Model
{
    protected $fillable = [
        'user_id', 'account_id', 'customer_id'
    ];

    /**
     * Get the user that owns this stripe 
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
