<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Classes\Card;
use Exception;
use App\Classes\Payment;

class PaymentController extends Controller
{
    /**
     * send money to the fulfiller
     * Input: card_id, amount, want_id, to_user
     */
    public function payFulfiller(Request $request)
    {
        try{
            $pay = new Payment($request->to_user,  $request->amount,$request->want_id);
            $pay->pay($request->card_id);
            return response()->json(['message'=> 'Your payment is successful'], 200);
        }catch(Exception $e){
            return $e->getMessage();
            return "Something went wrong while trying to make payment. Please try again";
        }
    }

    /**
     * Get the current balance
     */
    public function getBalance(){
        try{
            \Stripe\Stripe::setApiKey("sk_test_7DFayyE5PlPHvjyRAv07KC9p");
            $balance = \Stripe\Balance::retrieve(
                ["stripe_account" => Auth::user()->stripe->account_id]
            );
            return $balance;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Get all the transactions for this user 
     */
//     \Stripe\Stripe::setApiKey("sk_test_7DFayyE5PlPHvjyRAv07KC9p");

// \Stripe\Charge::all(["limit" => 3]);

    
}
