<?php 
namespace App\Classes;
use Auth;
use Exception;
use App\Stripe;
use App\Want;
use Illuminate\Http\Request;

class Transaction{
    function __construct(){
        //set stripe key 
        \Stripe\Stripe::setApiKey(env("STRIPE_API_SECRET"));
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


}
