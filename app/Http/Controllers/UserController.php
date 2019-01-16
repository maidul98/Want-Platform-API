<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    /**
     * Get profile of a user. Each profile has rating attached
     */
    public function profile($user){
        try{
            $profile = User::where('id', $user)->with('rating')->first();
            return $profile;
        }catch(Exception $e){

        }
    }

    /**
     * Get avatar URL for current user
     */
     public function getAvatar(){
         return Auth::user()->avatar;
     }

     /**
      * add tagline and profile details
      */
      public function addProfileDetails(){
          
      }
}
