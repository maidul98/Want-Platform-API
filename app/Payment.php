<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Stripe\Error\Card;
use Cartalyst\Stripe\Stripe;
use App\Http\Requests\addCard;
use Illuminate\Http\Request;
use App\Http\Requests\editCard;
use Auth;
use Exception;
use App\Stripe as StripeModel;
use App\Want;

class Payment extends Model
{
    public  $stripe; 

    public function __construct () {
        $this->stripe = Stripe::make("sk_test_7DFayyE5PlPHvjyRAv07KC9p"); 
      }

    /**
     * From current user, pay another user.
     * If all is well, does nothing. Throw Exception otherwise.
     */
    public function pay($amount, $toAccount, $cardId){

        try{
            // \Stripe\Stripe::setApiKey("sk_test_7DFayyE5PlPHvjyRAv07KC9p");
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

        }catch(Exception $e){
            throw new Exception("Something went wrong when trying process payment");
        }
    }

    /**
     * Refund
     */
    public function refound(){

    }


    
}
