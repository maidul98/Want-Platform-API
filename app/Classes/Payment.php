<?php 

namespace App\Classes;
use Auth;
use Exception;
use App\Want;
use Cartalyst\Stripe\Stripe;
use App\Stripe as StripeModel;
use Illuminate\Http\Request;
use App\Card;

class Payment{
    public $amount, $toUserID, $want_id;
    public function __construct($toUserID, $amount, $want_id){
        //set stripe key 
        \Stripe\Stripe::setApiKey(env("STRIPE_API_SECRET")); 
        
        //find the account id where the money should be sent
        $this->toUserID = StripeModel::where('user_id', $toUserID)->account_id;

        //set amunt to be paid
        $this->amount = $amount;

        //set want id 
        $this->want_id = $want_id;
    }

    /**
     * From current user, pay another user.
     * If all is well, does nothing. Exception otherwise.
     * Input: card_id ( the card that the user wants to use)
     */
    public function pay($card_id, $mark_as_complete = false){
        try{
            // \Stripe\Stripe::setApiKey("sk_test_7DFayyE5PlPHvjyRAv07KC9p");
            $charge = \Stripe\Charge::create([
                "amount" => $this->amount,
                "currency" => "usd",
                'customer' => Auth::user()->stripe->customer_id,
                'card' => $card_id,
                "destination" => [
                  "amount" => $this->amount,
                  "account" => $this->toUserID,
                ],
              ]);

            // if the charge went through
            if($charge && $mark_as_complete){
                // add it to the transation
                // mark the 
            }

        }catch(Exception $e){
            throw new Exception("Something went wrong when trying process payment");
        }
    }


    /**
     * Make a promise to someone
     */
    public function makePromise(){
        
    }
}