<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Want extends Model {
    use Searchable;
    
    protected $fillable = ['title', 'description', 'user_id', 'cost', 'status'];

    /**
     * A user has Want(s)
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    /**
     * A want belongs to a bookmark
     */
    public function bookmark(){
        return $this->belongsTo('App\Bookmark', 'want_id');
    }

    /**
     * Get the status of the Want 
     */
    public function getStatusAttribute($value)
    {
        return $value;
    }
}
