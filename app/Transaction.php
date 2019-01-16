<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'want_id', 'fulfiller_id', 'amount_paid'];

    /**
     * Creates a transaction log. Throw Exception if transaction is not made.
     */
    public static function create($user_id, $want_id, $fulfiller_id, $amount_paid){
        try{
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->want_id = $want_id;
            $transaction->fulfiller_id = $fulfiller_id;
            $transaction->amount_paid = $amount_paid; 
            $transaction->save();
        }catch(Exctrion $e){
            throw new Exception("Somthing went wrong when trying to create transaction"); 
        }

    }
}
