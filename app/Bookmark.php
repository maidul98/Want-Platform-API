<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $fillable = [
        'user_id', 'want_id'
    ];
    /**
     * Each bookmark belongs to a want 
     */
    public function want(){
        return $this->belongsTo('App\Want');
    }

    /**
     * Each bookmark has many wants
     */
    public function bookmarks(){
        return $this->hasOne('App\Want');
    }
}
