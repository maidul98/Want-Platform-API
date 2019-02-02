<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\Card;
use Exception;

class PaymentController extends Controller
{
    /**
     * Returns a token of the card informtion entred by the user  
     * */
    public function test(Request $request){
        $v = new Card(1);
        // return $request;

        try{
            return $v->removeCard($request);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
}
