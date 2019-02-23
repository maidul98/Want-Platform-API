<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
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
        return $this->hasMany('App\Want');
    }
}
