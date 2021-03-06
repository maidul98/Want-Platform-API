<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\User;
use App\PasswordReset;

class PasswordResetController extends Controller
{
    /**
     * Create token for passwod reset
     * input:email
     * return: message  
     */
    public function create(Request $request){
        $request->validate(['email'=> 'email|string|required']);
        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'message' => 'We can"t find a user with that e-mail address.'
            ], 404);
        }

        //add token 
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60)
             ]
        );
        if($user && $passwordReset){
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );

            return response()->json([
                'message' => 'We have e-mailed your password reset link!'
            ]);
        }
    }

    /**
     * Find token password rest
     * input: $token 
     * return: message
     */
    public function find($token){
        $passwordReset = PasswordReset::where('token', $token)->first();

        if(!$passwordReset){
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }

        if(Carbon::parse($passwordReset->update_at)->addMinutes(720)->isPast()){
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }else{
            return response()->json($passwordReset);
        }


    }

    /**
     * Reset password
     *Input: email, password, token
     * @return [string] message
     */
    public function reset(Request $request){
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);
        
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();

        if(!$passwordReset){
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }

        $user = User::where('email', $passwordReset->email)->first();

        if(!$user){
            return response()->json([
                'message' => 'We can\'t find a user with that e-mail address.'
            ], 404); 
        }

        $user->password = bcrypt($request->password);
        $user->save();

        $passwordReset->delete();

        $user->notify(new PasswordResetSuccess($passwordReset));

        return response()->json($user);
    }

}
