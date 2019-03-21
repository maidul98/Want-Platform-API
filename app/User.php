<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use Searchable;
    use \Staudenmeir\EloquentEagerLimit\HasEagerLimit; //limit the number of comments per want 

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name','email', 'password', "stripe_id", 'customer_id',
        'tag_line', 'description'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'email', 'password', 'email_verified_at', 'last_name', 'remember_token'
    ];

    /**
     * Get all Wants by this user
     */
    public function wants(){
        return $this->hasMany('App\Want'); 
    }

    /**
     * Users can send many messages
     */

     public function messages(){
         return $this->hasMany('App\Message');
     }

    /**
     * Get Stripe account number and customer id of this user
     */
    public function stripe()
    {
        return $this->hasOne('App\Stripe');
    }

    /**
     * Get the transactions for this user
     */
    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    /**
     * Get the review(s) for this user
     */
    public function reviews()
    {
        return $this->hasMany('App\Review');
    }

    /**
     * Get the rating for this user
     */
    public function rating()
    {
        return $this->hasOne('App\Rating');
    }

     /**
     * Get all the conversations that this user is in
     */
    public function conversations()
    {
        return $this->hasMany('App\Conversation');
    }

    /**
     * Each user has many bookmarks
     */
    public function bookmarks(){
        return $this->hasMany(Bookmark::class);
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
        $array['user'] = $user;
        return $array;
        }

}
