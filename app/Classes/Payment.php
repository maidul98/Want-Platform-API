<?php 

namespace App\Classes;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Want;
use Cartalyst\Stripe\Stripe;
use App\Stripe as StripeModel;
use Illuminate\Http\Request;
use App\Card;
use App\User;
use App\Transaction;

class Payment{
    public $amount, $to_user_id, $want_id;

    /**
     * All payments will have a user that one will pay to, an amount they will pay, and it will be related to a Want.
     * You will only be able to make transactions associated with a Want. 
     */
    public function __construct($to_user_id, $amount, $want_id, $complete = false){
        //set stripe key 
        \Stripe\Stripe::setApiKey(env("STRIPE_API_SECRET")); 

        $this->amount = $amount;

        //find the account id where the money should be sent
        $this->to_user_id = $to_user_id;

        //set want id 
        $this->want_id = $want_id;

        // check to see if this post should be marked as complete
        $this->complete = $complete;

        // check if Want is taken already 
        if(Want::findOrFail($want_id)->status != 1){
            throw new Exception('This post is already assigned to another user.');
        }
    }

    /**
     * This function allows for the current user to pay another user.
     * If all is well, does nothing. Exception otherwise.
     * Input: card_id ( the card that the current user wants to use)
     */
    public function pay($card_id){
        $charge = \Stripe\Charge::create([
            "amount" => $this->amount,
            "currency" => "usd",
            'customer' => Auth::user()->stripe->customer_id, // the current user is the customer 
            'card' => $card_id, // the card they will use to pay 
            "destination" => [
                "amount" => $this->amount,
                "account" => User::find($this->to_user_id)->stripe->account_id, //sending money to this users stripe account 
            ],
        ]);

        // if the charge went through
        if($charge){
            // make log of Transaction
            Transaction::create(
                ['user_id' => Auth::user()->id,
                 'fulfiller_id' => $this->to_user_id,
                 'want_id'=> $this->want_id, 
                 'amount_paid'=>$this->amount, 
                 'status'=>1]);

            // change the status of the Want post if this payment markes it complete 
            if($this->complete){
                Want::findOrFail($this->want_id)->update(['status' => 4]);
            }
            
        }else{
            throw new Excaption('The charge did not process.');
        }
    }


    /**
     * Make a milstone payment to a user. 
     * The Wanter makes a milstone, once they give it to the fulfiler, they are charged and Want App holds on to the money. 
     */
    public function makePromise(){
        
    }
}