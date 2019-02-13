<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
    *
    * @return \Illuminate\Http\Response
    */
    public function redirectToProviderGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallbackGoogle()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
        } catch (Exception $e) {
            return response()->json(['error' => 'Somthing went wrong'], 400);
        }

        // // check if they're an existing user
        $existingUser = User::where('email', $user->email)->first();
        if($existingUser){
            $token = $existingUser->createToken('login')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            $register = new Register($user->user['given_name'], 
            $user->user['family_name'], $user->user['email'], null);
            $register->register();
            Auth::loginUsingId($register->user->id);
            return Auth::user();
        }
    }
}
