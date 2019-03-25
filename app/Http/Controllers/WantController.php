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

use App\Notifications\Notifyfulfiller;

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
     * Accect the want: creates chat, add want to active wants
     * Input: want_id 
     */
    public function acceptWant(Request $request){
        try{
            if(Want::findOrFail($request->want_id)->status == 1){
                //update want status

                //find want
                $want = Want::findOrFail($request->want_id);
                $wanter = User::findOrFail($want->user_id);
                
                //create a conversation
                Conversation::create(['wanter_id' => $wanter->id, 'fulfiller_id' => Auth::user()->id, 'want_id' => $request->want_id]);

                //notify the user that he has been accpetded
                User::find(1)->notify(new Notifyfulfiller($wanter, Auth::user(), "Congrants! You have been chosen", $want));
            }
        }catch(Excation $e){
            return $e->getMessage();
        }
    }

    public function all(){
        return Want::all();
    }
    

}
