<?php 

namespace App\Classes;
use Auth;
use Exception;
use App\Stripe;
use App\Want;
use Illuminate\Http\Request;

class Card{
    public $customer_id, $stripe;
    public function __construct(){
        //set the stripe customer id 
        $this->customer_id =  Stripe::where('user_id', Auth::user()->id)->firstOrFail()->customer_id;
        
        //set stripe key 
        \Stripe\Stripe::setApiKey(env("STRIPE_API_SECRET")); 
    }

    /**
     * Takes in a request and return a token for that card.
     * Throws error if card has issues  
     * */
    public function token(Request $request){

        try{
            $token = \Stripe\Token::create([
                'card' => [
                    'number' => $request->get('card_no'),
                    'exp_month' => $request->get('ccExpiryMonth'),
                    'exp_year' => $request->get('ccExpiryYear'),
                    'cvc' => $request->get('cvvNumber'),
                ],
            ]);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
        return $token;
    }

    /**
     * Get all cards of user. Returns a list of cards
     */
    public function getCards(){
        $cards = \Stripe\Customer::retrieve($this->customer_id)->sources->all();
        return $cards;
     }


     /**
     * Add a card to this customers account
     */
    public function addCard(Request $request){
        $token = $this->token($request);
        $customer = \Stripe\Customer::retrieve($this->customer_id);
        $customer->sources->create(["source" => $token]);
    }

    /**
     * Delete an existing card from customers account. 
     * Returns true if deleted, others returns false 
     * Input: card_id
     */
    public function remove(Request $request){
        $customer = \Stripe\Customer::retrieve($this->customer_id);
        $customer->sources->retrieve($request->card_id)->delete();
    }
    
}