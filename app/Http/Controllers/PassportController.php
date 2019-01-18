<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cartalyst\Stripe\Stripe;
use App\Stripe as StripeTable;
use Exception;
use App\Rating;
use Hash;
use Socialite;

use App\Http\Requests\Register;
class PassportController extends Controller
{
    /**
     * Handles Registration Request.
     * Create user, set Stripe details,
     * Create empty rating for user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Register $request){
        try{
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
    
            \Stripe\Stripe::setApiKey("sk_test_7DFayyE5PlPHvjyRAv07KC9p");

            $stripeAccount = \Stripe::account()->create([
                "country" => "US",
                'email' => $request->email,
                "type" => "custom",
            ]);

            $customer = \Stripe\Customer::create([
                "email" => $request->email,
                ]
            );

            $user->save();

        }catch(Exception $e){
            return $e->getMessage();
        }
        
        //Accept Stripe TOS
        $stripeAccount = \Stripe\Account::retrieve($stripeAccount['id']);
        $stripeAccount->tos_acceptance->date = time();
        $stripeAccount->tos_acceptance->ip = $_SERVER['REMOTE_ADDR'];
        $customer->save();
        $stripeAccount->save();
        
        //Create Stripe details 
        $stripe = StripeTable::create([
            'user_id' => $user->id,
            'account_id' => $stripeAccount['id'],
            'customer_id' => $customer['id'],
        ]);
        $stripe->save();

        //Create rating for user 
        $rating = new Rating();
        $rating->user_id = $user->id;
        $rating->current_rating = 5;
        $rating->total_ratings = 0;
        $rating->save();
        
        $token = $user->createToken('login')->accessToken;

        return response()->json(['token' => $token], 200);
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
    public function redirectToProvider()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
            // return $user->user['given_name'];
            // return $user->email;
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        // // check if they're an existing user
        $existingUser = User::where('email', $user->email)->first();
        if($existingUser){

            //return token to login the user
            $token = auth()->user()->createToken('login')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            // create a new user
            User::create([
                'first_name' => $user->user['given_name'],
                'last_name' => $user->user['family_name'],
                'email' => $user->user['email'],
                'password' => null,
            ]);

            // $newUser                  = new User;
            // $newUser->name            = $user->name;
            // $newUser->email           = $user->email;
            // $newUser->google_id       = $user->id;
            // $newUser->avatar          = $user->avatar;
            // $newUser->avatar_original = $user->avatar_original;
            // $newUser->save();
            
            //return token to login the user
            $token = auth()->user()->createToken('login')->accessToken;
            return response()->json(['token' => $token], 200);
        }
    }

}
