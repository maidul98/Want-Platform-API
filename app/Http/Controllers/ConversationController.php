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
            return Conversation::where(function ($query) {
                $query->where('fulfiller_id', '=', Auth::user()->id)->orWhere('wanter_id', '=', Auth::user()->id);
            })->with('want', 'fulfiller', 'wanter')->orderBy('updated_at', 'desc')->withCount(
                ['unseen' => function ($query) {
                $query->where('user_id', '!=', Auth::user()->id)->where('seen', '=', 0);
            }])->get();
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
