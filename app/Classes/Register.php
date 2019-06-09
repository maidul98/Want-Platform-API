<?php 
namespace App\Classes;
use Auth;
use Exception;
use App\Stripe;
use App\Want;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Rating;
use App\User;

use Laracombee;

class Register{
    public $first_name, $last_name, $email, $password;
    public $stripe_account_id, $stripe_cus_id, $user;

    /**
     * Init all the feilds needed for registering a user
     */
    public function __construct($first_name, $last_name, $email, $password = null){
        //set stripe key 
        \Stripe\Stripe::setApiKey(env("STRIPE_API_SECRET"));

        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->password = $password;

    }

    /**
     * Create the new user and saves it to a feild var
     * Throws error if something goes wrong 
     */
    public function create_user(){ 
        DB::beginTransaction();
        try{
            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => bcrypt($this->password)
            ]);
    
            //save the user 
            $this->user = $user;
    
            //make user on Recombee
            $this->createUserOnRecombee();
    
            //make user on Stripe
            $this->set_stripe();
    
            //give the user a rating 
            $this->createRatings();
    
            // return user token on successful registration
            return $this->token();
        }catch(Exception $e){
            DB::rollback();

            return $e->getMessage();
            // throw new Exception('Something went wrong, please try again');
        }   
    }

    /**
     * Sets the stripe account and stripe cus id
     * Throws error if accounts are not created
     */
    public function set_stripe(){
        //make stripe account for user 
        $stripeAccount = \Stripe\Account::create([
            "country" => "US",
            'email' => $this->email,
            "type" => "custom",
        ]);

        //set id 
        $this->stripe_account_id = $stripeAccount['id'];

        // make stripe customer account for receiving payments 
        $customer = \Stripe\Customer::create([
            "email" => $this->email,
            ]
        );

        //set customer id 
        $this->stripe_cus_id = $customer['id'];

        //save stripe customer/account deltails to DB
        $this->createStripeTable();
    }

    /**
     * return login token
     */
    public function token(){
        $token = $this->user->createToken('login')->accessToken;
        return response()->json(['token' => $token], 200);
    }

    /**
     * Create record for stripe and accpet the TOC of stripe
     */
    public function createStripeTable(){
        $stripe = Stripe::create([
            'user_id' => $this->user->id,
            'account_id' => $this->stripe_account_id,
            'customer_id' => $this->stripe_cus_id,
        ]);

        $stripeAccount = \Stripe\Account::retrieve($this->stripe_account_id);
        $stripeAccount->tos_acceptance->date = time();
        $stripeAccount->tos_acceptance->ip = $_SERVER['REMOTE_ADDR'];
        $stripeAccount->save();
    }

    /**
     * Create ratings
     */
    public function createRatings(){
        $rating = new Rating();
        $rating->user_id = $this->user->id;
        $rating->current_rating = 5;
        $rating->total_ratings = 0;
        $rating->save();
    }

    /**
     * Add this user to Recombee database
     */
    public function createUserOnRecombee(){
        $user = User::findOrFail($this->user->id);

        $addUser = Laracombee::addUser($user);

        Laracombee::send($addUser)->then(function () {
        // Success.
        })->otherWise(function ($error) {
        // Handle Exeption.
        })->wait();
    }
}