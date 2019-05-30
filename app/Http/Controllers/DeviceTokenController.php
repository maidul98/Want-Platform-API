<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DeviceTokenController extends Controller
{
    /**
     * Save the user's device token to the DB
     * input:token
     */
    public function addOrUpdateToken(Request $request){
        $current_token = Auth::user()->device_token;

        if(!empty($current_token) && $current_token != $request->token){
            DB::table('users')->where('id', Auth::user()->id)->update(['device_token'=>$request->token]);
        }else if(empty($current_token)){
            DB::table('users')->where('id', Auth::user()->id)->update(['device_token'=>$request->token]);
        }
        
        return response()->json(['message'=> 'Device token has been added'], 200);
    }
}
