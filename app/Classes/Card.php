<?php 

namespace App\Classes;
use Auth;
use Exception;
use App\Stripe;
use App\Want;
use Illuminate\Http\Request;

class Card{
    public $customer_id;
    public function __construct($user_id){
        $this->customer_id =  Stripe::where('user_id')->customer_id;
    }

    /**
     * Takes in a request and return a token for that card.
     * Throws error if card has issues  
     * */
    public function getCardToken(Request $request){
        try{
            $token = $this->stripe->tokens()->create([
                'card' => [
                'number' => $request->get('card_no'),
                'exp_month' => $request->get('ccExpiryMonth'),
                'exp_year' => $request->get('ccExpiryYear'),
                'cvc' => $request->get('cvvNumber'),
                ],
            ]);
        }catch(\Cartalyst\Stripe\Exception\CardErrorException $e){
            throw new Exception($e->getMessage());
        }
        return $token;
    }
}