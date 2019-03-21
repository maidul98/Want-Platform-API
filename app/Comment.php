<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use \Staudenmeir\EloquentEagerLimit\HasEagerLimit; //limit the number of comments per want 
    
    /**
     * Each comment belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Each comment has many replies
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
    
}
