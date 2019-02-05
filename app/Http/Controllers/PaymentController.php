<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\Card;
use Exception;
use App\Classes\Transaction;

class PaymentController extends Controller
{
    /**
     * Returns a token of the card informtion entred by the user  
     * */
    public function test(Request $request){
        $x = new Transaction();
        return $x->pay($request);
    }

    /**
     * send money to the fulfiller
     * Input: card_id, amout, toAccount 
     */
    public function payFulfiller(Request $request)
    {
        try{
            $pay = new Transaction();
            $pay->pay($request);
            return response()->json(['message'=> 'Your payment is successful'], 200);
        }catch(Exception $e){
            return "Something went wrong while trying to make payment. Please try again";
        }
    }

    
}
