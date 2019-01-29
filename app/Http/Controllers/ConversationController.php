<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\Conversation;
use Illuminate\Support\Facades\Auth;
use Exception;

class ConversationController extends Controller
{
    /**
     * Get all conversations the current user is in 
     */

     public static function getConversations(){
         try{
            return Conversation::where(function ($query) {
                $query->where('fulfiller_id', '=', Auth::user()->id)
                      ->orWhere('wanter_id', '=', Auth::user()->id);
            })->with('want', 'fulfiller', 'wanter')->get();

            // where(function ($query) {
            //     $query->where('fulfiller_id', '=', Auth::user()->id)
            //           ->orWhere('wanter_id', '=', Auth::user()->id);
            // })->get();

         }catch(Exception $e){
             return $e->getMessage();
         }
     }

     /**
      * Create a new converstion between these users or throw an error if something goes wrong
      */
     public function createConversation($wanter_id, $fulfiler_id){
        try{
           $convo = new Conversation();
            $convo->wanter_id = $wanter_id;
            $convo->fulfiller_id = $fulfiler_id;
            $convo->save();
        }catch(Exception $e){
            throw new Exception('Could not create converstion'); 
        }
    }
}
