<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use App\User;
use App\Review;
use App\Rating;
use App\Want;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    /**
     * Get profile of a user. Each profile has rating attached
     */
    public function profile($user){
        try{
            $userInfo = User::where('id', $user)->with('rating')->get();
            $review = Review::where('fulfiller_id', $user)->with(array('want' => function($query)
{
                $query->select('id', 'title', 'category_id');

            }))->with('user')->simplePaginate(6);

            $total_fulfil = Want::where(['user_id' => $user, 'status' => 3])->count();
            $total_reviews = Review::where('fulfiller_id', $user)->count();
            $json = ['user'=> $userInfo, 'review' => $review, 'stats' => ['total_fulfillment' => $total_fulfil, 'total_reviews'=> $total_reviews]];
            return $json;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Get avatar URL for current user
     */
     public function getAvatar(){
         return Auth::user()->avatar;
     }
}
