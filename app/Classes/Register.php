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
        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => bcrypt($this->password)
        ]);

        // set user if created 
        if($user){
            $this->user = $user;
        }

        $this->user = $user->id;
    }

    /**
     * Sets the stripe account and stripe cus id
     * Throws error if accounts are not created
     */
    public function set_stripe(){
        try{
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

        }catch(Exception $e){
            // return $e->getMessage();
            return 'something is wrong with stripe';
        }
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
     * register the user 
     */
    public function register(){
        DB::beginTransaction();
        try{
            $this->create_user();
            $this->set_stripe();
            $this->createStripeTable();
            $this->createRatings();
            return $this->token();
        }catch(Exception $e){
            DB::rollback();
            return "Something went wrong, please try again later!";
        }
    }

}