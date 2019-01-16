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
     * From current your, pay another user.
     * If all is well, does nothing. Throw Exception otherwise
     */
    public function pay($amount, $toAccount, $cardId){

        try{
            \Stripe\Stripe::setApiKey("sk_test_7DFayyE5PlPHvjyRAv07KC9p");

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


    /**
     * Edit a exiting card from customers account
     * Returns message on whether card has been succsefully changed or not 
     */
    public function editCard($customerId, $cardToken, editCard $request){
        try{
            $this->stripe->cards()->update($customerId, $cardToken, [
            'exp_month' => $request->get('ccExpiryMonth'),
            'exp_year' => $request->get('ccExpiryYear'),
            ]);

            return "Your card has been updated";

        }catch(Exception $e){
            return "Something went wrong, try again";
        }
    }

    /**
     * Delete an existing card from customers account. 
     * Returns true if deleted, others returns false 
     */
    public function removeCard($customerId, $cardToken){
        try{
            if($this->stripe->cards()->delete($customerId, $cardToken)) return "Your card has been removed successfully";
        }catch(Exception $e){
            return "Something went wrong, try again";
        }
    }

    /**
     * Add a card to customer's account
     */
    public function addCard($customerId, $cardToken){
        try{
            $customer = $this->stripe->customers()->find($customerId);
            $this->stripe->cards()->create($customerId, $cardToken['id']);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }

        return 'You have successfully added a new payment method';
    }

    /**
     * Get all cards of user. Returns a list of cards
     * If they have no cards, return false
     */
     public function getAllCards(){
        $cards = $this->stripe->cards()->all(Auth::user()->stripe->customer_id);
        $allDetials = array();
        foreach ($cards['data'] as $card) {
            $singleDetials = array(
                "id" => $card['id'],
                "last4" => $card['last4'],
                "brand" => $card['brand'],
                "exp_month" => $card['exp_month'],
                "exp_year" => $card['exp_year'],
            );

            array_push($allDetials, $singleDetials);
        }

        if(count($allDetials) > 0) return $allDetials;

        return false;
     }

    /**
     * Returns a token of the card informtion entred by the user  
     * */
    public function getCardToken(addCard $request){
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
