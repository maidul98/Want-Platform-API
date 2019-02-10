<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\Card;
use Auth;
use Exception;

class CardsController extends Controller
{
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
     * returns succsess message or shows error 
     * Input:card_id
     */
    public function remove(Request $request){
        try{
            $card = new Card();
            $card->remove($request);
            return 'You have successfully removed a payment method';
        }catch(Exception $e){
            return $e->getJsonBody();
            return "Something went wrong, try again";
        }
    }

    /**
     * Add a card to customer's account
     */
    public function add(Request $request){
        try{
            $card = new Card();
            $card->addCard($request);
            return 'You have successfully added a new payment method';
        }catch(Exception $e ){
            return $e->getMessage();
        }
    }

    /**
     * Get all cards of user. Returns a list of cards
     * If they have no cards, return false
     */
     public function getAll(){
         try{
            $card = new Card();
            return $card->getCards();
         }catch(Exception $e){
            return "Something went wrong";
         }
        
     }
}
