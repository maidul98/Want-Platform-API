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
use App\Conversation;

use Laracombee;
use App\Notifications\Notifyfulfiller;
use Illuminate\Support\Facades\DB;

class WantController extends Controller
{

    /**
     * Store a newly created Want 
     * Input: title, description, cost, category
     */
    public function store(StoreWant $request)
    {   
        try{
            $want = new Want;
            $want->title = $request->input("title");
            $want->user_id = Auth::id();
            $want->description = $request->input("description");
            $want->cost = $request->input("cost");
            $want->status = 1;
            $want->category_id = $request->input("category");
            $want->save();

            //add to recombe 
            $addWant = Laracombee::addItem($want);
            Laracombee::send($addWant)->then(function () {
              // Success.
            })->otherWise(function ($error) {
              // Handle Exeption.
            })->wait();
            
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
            $want = Want::where('id', $id)->with('user')->with(array('bookmark' => function($query){ $query->where('user_id', Auth::user()->id); }))->with(array('comments' => function($query) { $query->with('replies.user')->with('user');}))->first();
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
     * Assign a fulfiller to a Want.
     * Input: want_id, fulfiller_id
     */
    public function assign_to_fulfiller(Request $request){
        try{
            // make sure the user exists
            User::findOrFail($request->fulfiller_id);

            $want = Want::findOrFail($request->want_id);
            if($want->fulfiller_id == null && $want->user_id == Auth::user()->id) {
                Want::findOrFail($request->want_id)->update(['fulfiller_id'=>$request->fulfiller_id, 'status'=>3]);
                return "You have successfully assigned a fulfiller to this Want";
            }else{
                return "Something went wrong";
            }
        }catch(Excation $e){
            return response()->json(['error'=> 'Something went wrong, please try again'], 400);  
        }
    }

    /**
     * Get all the Wants the user posted with the status of each Want 
     */
     public function UserWants(Request $request){
         try{
             return Want::where(['user_id'=>Auth::user()->id])->get();
         }catch(Excetion  $e){
            return response()->json(['error'=> 'Something went wrong, please try again'], 400);  
         }
     }

     /**
     * Get all the Wants that are marked active
     */
    public function activeWants(Request $request){
        try{
            return Want::where(['user_id'=>Auth::user()->id, 'status_id' => 1])->get();
        }catch(Excetion  $e){
           return response()->json(['error'=> 'Something went wrong, please try again'], 400);  
        }
    }

    /**
     * Get all the Wants that are marked active
     */
    public function inProgress(Request $request){
        try{
            return Want::where(['user_id'=>Auth::user()->id, 'status_id' => 3])->get();
        }catch(Excetion  $e){
           return response()->json(['error'=> 'Something went wrong, please try again'], 400);  
        }
    }

}
