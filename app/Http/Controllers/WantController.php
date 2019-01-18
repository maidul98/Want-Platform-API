<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Exception;

// Validation 
use Illuminate\Http\Request;
use App\Http\Requests\StoreWant;
use App\Http\Requests\pay;

//Models
use App\Want;
use App\User;
use App\Stripe;
use App\Transaction;
use App\Payment;

use Illuminate\Support\Facades\DB;


// require_once base_path('vendor/paralleldots/apis/autoload.php');

class WantController extends Controller
{

    /**
     * Store a newly created Want 
     * Input: title, description, cost, category
     */
    public function store(StoreWant $request)
    {   
        try{
//             $text_list = "[\"drugs are fun\",\"don't do drugs, stay in school\",\"lol you a fag son\",\"I have a throat infection\"]";
// echo abuse_batch($text_list);
            $want = new Want;
            $want->title = $request->input("title");
            $want->user_id = Auth::id();
            $want->description = $request->input("description");
            $want->cost = $request->input("cost");
            $want->status = 1;
            $want->category_id = $request->input("category");
            $want->save();
            
            return response()->json(['data' =>$want, 'message'=> 'Your want has been posted'], 200);

        }catch(Exception $e){
            return $e->getMessage();
            return response()->json(['error'=> 'Something went wrong, please try again'], 400);  
        }
    }

    /**
     * Get a single want
     * Input: id 
     */
    public function show($id)
    {
        try{
            $want = Want::findOrFail($id);
            $want->user;
            return response()->json(['want' => $want]);
        }catch(Exception $e){
            return response()->json(['error'=> 'Something went wrong, please try again'], 400);  
        }
    }

    /**
     * Update a want
     */
    public function update(Request $request, $id)
    {
        try{
            $want = Want::where('user_id',Auth::user()->id)->findOrFail($id);
            $want->update($request->all());
            $want->save();
            return response()->json(['data' =>$want, 'message'=> ''], 200); 
        }catch(Exception $e){
            return response()->json(['error'=> 'Something went wrong, please try again'], 400);  
        }
    }

    /**
     * Delete a Want
     * Input:id
     */
    public function destroy($id)
    {
        try{
            $user_want = Want::findOrFail($id)->user_id;

            if($user_want == Auth::user()->id){
                Want::findOrFail($id)->delete();
            }else{
                throw new Exception("Something went wrong");
            }
            
            return response()->json(['message'=> 'Your want has been deleted'], 200); 

        }catch(Exception $e){
            return response()->json(['error'=> 'Something went wrong, please try again'], 400);  
        }
    }


    /**
     * Mark Want as complete.
     *
     */
    public function CompleteWant(Pay $request)
    {
        try{

            $payment = new Payment();
            $want = Want::findOrFail(1)->where('user_id', Auth::user()->id)->first();

            if($want->status == 2){
                $payment->pay($request->get('amount'), $request->get('to'), $request->get('card_id'));

                $fulfiller_id = Stripe::findOrFail(Stripe::where('account_id', $request->get('to'))->first()->id)->user()->first();
                $user_id = Auth::user()->id;
                $want_id = $request->get('want_id');
                $amount_paid = $request->get('amount'); 
                
                Transaction::create($user_id, $want_id, $fulfiller_id->id, $amount_paid);

                //update Want status
                $want->status = 3;
                $want->save();

                return "All paid done, don't forget to rate"." ".$fulfiller_id->name;
            }else{
                return;
            }

        }catch(Exception $e){
            return "Something went wrong while trying to make payment. Please try again";
        }
    }
}
