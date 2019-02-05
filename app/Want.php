<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Want extends Model {
    protected $fillable = ['title', 'description', 'user_id', 'cost', 'status'];

    /**
     * A user has Want(s)
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    /**
     * Get the status of the Want 
     */
    public function getStatusAttribute($value)
    {
        return $value;
    }
}
