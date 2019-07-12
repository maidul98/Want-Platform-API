<?php
namespace App\Http\Controllers;

use App\Classes\Register;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cartalyst\Stripe\Stripe;
use App\Stripe as StripeTable;
use Exception;
use App\Rating;
use Hash;
use Socialite;
use App\Http\Requests\Register as RegVal;

// use App\Http\Requests\Register;
class PassportController extends Controller
{
    /**
     * Handles Registration Request.
     * Create user, set Stripe details,
     * Create empty rating for user
     * If all is well, gets a login token, otherwise gets message
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegVal $request){
        try{
            $register = new Register($request->first_name, 
            $request->last_name, $request->email, $request->password);
            return $register->create_user();
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
 
    /**
     * Handles Login Request
     *
     */
    public function login(Request $request){
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('login')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Your password or email is wrong'], 401);
        }
    }
 
    /**
     * Returns Authenticated User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function details()
    {
        return response()->json(['user' => auth()->user()->makeVisible(['email', 'last_name'])], 200);
    }

    public function logout(){   
        if (Auth::check()) {
            Auth::user()->token()->delete();
            return response()->json(['status' =>'1'], 200); 
        }else{
            return response()->json(['status' =>'0'], 400);
        }
    }

    /**
     * Handles forget password requests
     *
     */
    public function forgot(Request $request){
        
    }

    /**
     * Change password 
     * input: current-password, new-password
     * Return: Gives you error message if something goes wrong, succsess message otherwise 
    */
    public function changePassword(Request $request){
        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|min:6',
        ]);
        
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return response()->json(['error'=> 'Your password does not match the one on file, please try again'], 400); 
        }

        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();
        return response()->json(['message'=> 'Your password have been changed successfully'], 200); 
    }

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
            return $user->email;
            // // check if they're an existing user
            // $existingUser = User::where('email', $user->email)->first();
            // if($existingUser){
            //     $token = $existingUser->createToken('login')->accessToken;
            //     return response()->json(['token' => $token], 200);
            // } else {
            //     $register = new Register($user->user['given_name'], 
            //     $user->user['family_name'], $user->user['email'], null);
            //     $register->register();
            //     Auth::loginUsingId($register->user->id);
            //     return Auth::user();
            // }
        } catch (Exception $e) {
            return response()->json(['error' => 'Somthing went wrong. The Google login was not successful.'], 400);
        }
    }

}
