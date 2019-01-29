<?php 
namespace App\Classes;
use Auth;
use Exception;
use App\Stripe;
use App\Want;

class Transaction{
    public $stripe;

    function __construct(){
        $this->stripe = Stripe::env("STRIPE_API_SECRET"); 
    }


    /**
     * Creates a transaction log.
     */
    public static function create($user_id, $want_id, $fulfiller_id, $amount_paid){
        $transaction = new Transaction();
        $transaction->user_id = $user_id;
        $transaction->want_id = $want_id;
        $transaction->fulfiller_id = $fulfiller_id;
        $transaction->amount_paid = $amount_paid; 
        $transaction->save();
    }

    /**
     * Pay a user give that the Want is complete 
     */
    public function pay($amount, $toAccount, $cardId){
        return $charge = \Stripe\Charge::create([
            "amount" => $amount,
            "currency" => "usd",
            'customer' => Auth::user()->stripe->customer_id,
            'card' => $cardId,
            "destination" => [
              "amount" => $amount,
              "account" => $toAccount,
            ],
          ]);
    }
}
