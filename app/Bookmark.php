<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
class Bookmark extends Model
{
    use Searchable;
    protected $fillable = [
        'user_id', 'want_id'
    ];

    protected $touches = ['want'];
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
