<?php

namespace App\Http\Controllers;

use App\Message;
use App\Conversation;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Get all conversations the current user is in 
     * Input: convo_id
     */
     public static function getConversations(Request $request){
        try{
             //check user belongs in this convo 
            $inConvo = Conversation::findOrFail($request->convo_id)->where(function ($query) {
                $query->where('wanter_id', Auth::user()->id)
                    ->orWhere('fulfiller_id', Auth::user()->id);
            })->firstOrFail();

            // //find the other user
            // $convo_info = Conversation::find($request->convo_id)->firstOrFail();
            if($inConvo->wanter_id != Auth::user()->id){
                $other_user_id = $inConvo->wanter_id;
            }else{
                $other_user_id = $inConvo->fulfiller_id;
            }

            // //count of unread messages 
            $unread_count = Message::where(['conversation_id'=> $request->convo_id, 'user_id' => $other_user_id, 'seen' => 0])->count();
            
            return Conversation::where(function ($query) {
                $query->where('fulfiller_id', '=', Auth::user()->id)
                      ->orWhere('wanter_id', '=', Auth::user()->id);
            })->with('want', 'fulfiller', 'wanter')->orderBy('updated_at', 'desc')->get()->push(['unread_count' => $unread_count]);

         }catch(Exception $e){
             return $e->getMessage();
         }
     }

     /**
      * Create a new converstion between these users or throw an error if something goes wrong
      * Input: wanter_id, fulfiller_id
      */
     public function createConversation(Request $request){
        try{
            if(!Conversation::where(['wanter_id' => Auth::user()->id, 'fulfiller_id'=> $request->fulfiller_id])->exists()){
                $convo = new Conversation();
                $convo->wanter_id = Auth::user()->id;
                $convo->fulfiller_id = $request->fulfiller_id;
                $convo->want_id = $request->want_id;
                $convo->save();
                return 'convo created';
            }else{
                return 'convo not created';
            }
        }catch(Exception $e){
            return $e->getMessage();
            return 'Could not create converstion';
        }
    }
}
