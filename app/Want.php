<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\User;
use App\bookmark;

class Want extends Model {
    use Searchable; //enable search by algolia 
    use \Staudenmeir\EloquentEagerLimit\HasEagerLimit; //limit the number of comments per want 

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
        return $this->belongsTo('App\Bookmark', 'id', 'want_id');
    }

    /**
     * Get the status of the Want 
     */
    public function getStatusAttribute($value)
    {
        return $value;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(){
    $array = $this->toArray();

    //find the user
    $user = User::findOrFail($this->user)->first();
    $bookmark = null;
    $array['user'] = $user;
    $array['bookmark'] = $bookmark;
    return $array;
    }


    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }


}
