<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Message;
use App\Conversation;
use Exception;


use App\Http\Controllers\ConversationController;

use App\Events\MessageSentEvent;

class MessageController extends Controller
{
    /**
     * Makes sure that you can only send messages in convos you are apart of.
     * Returns error if you are not allowed to access such  convo
     */
    
    // public function __construct(){
    //     try{
    //         Conversation::findOrFail(request()->convo_id)->where('wanter_id', Auth::user()->id)->
    //         orWhere('fulfiller_id', Auth::user()->id)->get();
    //     }catch(Exception $e){
    //        exit(var_dump(Auth::user()));
    //     }
    // }


    /**
     * Get all messages in this convoersation. 
     * Current user has to be apart of this convo
     */
    public function fetch(Request $request){
        try{
            return Conversation::findOrFail($request->convo_id)->where('wanter_id', Auth::user()->id)->
            orWhere('fulfiller_id', Auth::user()->id)->with(['fulfiller', 'wanter', 'messages'])->latest()->firstOrFail();
        }catch(Exception $e){
            return $e;
            return "Something went wrong";
        }

    }


    /**
     * Send a message to the conversation id with a message.
     * Input:convo_id, message
     * Returns an error if message is unable to send.
     */
    public function sendMessage(Request $request){
        try{
            Conversation::findOrFail($request->convo_id)->where('wanter_id', Auth::user()->id)->
            orWhere('fulfiller_id', Auth::user()->id)->firstOrFail();
            $message = new Message();
            $message->message = $request->message;
            $message->user_id = Auth::user()->id;
            $message->conversation_id = $request->convo_id;
            $message->save();
            
            broadcast(new MessageSentEvent($message, $request->convo_id, Auth::user()))->toOthers();

        }catch(Exception $e){
            return response()->json(['error'=> 'Your message could not be sent for an unknown reason'], 400);  
        }

    }
}
