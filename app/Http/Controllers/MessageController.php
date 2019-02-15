<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Message;
use App\Conversation;
use Exception;
use Image;
use Illuminate\Support\Facades\Storage;
use App\Attachment;


use App\Http\Controllers\ConversationController;

use App\Events\MessageSentEvent;
class MessageController extends Controller
{
    /**
     * Get all messages in this convoersation. 
     * Current user has to be apart of this convo
     * Input:convo_id
     */
    public function fetch(Request $request){
        try{
            // Check if user is in the chat
            $convo = Conversation::findOrFail($request->convo_id)->where(function ($query) {
                $query->where('wanter_id', Auth::user()->id)
                      ->orWhere('fulfiller_id', Auth::user()->id);
            })->exists();

            //Find out which user is the the other user 
            $convo_info = Conversation::find($request->convo_id)->first();

            if($convo_info->fulfiller_id != Auth::user()->id){
               $other_user_id = $convo_info->fulfiller_id; 
            }else{
               $other_user_id = $convo_info->wanter_id;  
            }

            //if the user is in the convo 
            if($convo){
                return Conversation::where(['id'=> $request->convo_id])->with(['fulfiller', 'wanter', 'messages.attachments'])->latest()->firstOrFail();
            }
        }catch(Exception $e){
            return $e->getMessage();
            return "Something went wrong";
        }

    }

    /**
     * Send a message to the conversation id with a message.
     * Input:convo_id, message
     * Returns an error if message is unable to send.
     */
    public function sendMessage(Request $request){
        $this->validate($request, [
            'attachment.*' => 'image|mimes:jpeg,png,jpg,gif|max:6000'
        ]);

        try{
            //check if user can send messages to this chat 
            // Conversation::findOrFail($request->convo_id)->where('wanter_id', Auth::user()->id)->
            // orWhere('fulfiller_id', Auth::user()->id)->firstOrFail();

            if($request->hasFile('attachment')){
                $array = [];
                foreach($request->file('attachment') as $file){
                    $filename = 'message-attachment/'.time().'.'.$file->getClientOriginalExtension();
                    array_push($array, $filename);
                    Storage::disk('s3')->put($filename, fopen($file, 'r+'), 'public');
                }
            }

            //save message
            $message = new Message();
            $message->message = $request->message;
            $message->user_id = Auth::user()->id;
            $message->conversation_id = $request->convo_id;
            $message->save();

            //create records for attachments
            if($request->hasFile('attachment')){
                foreach($array as $img){
                    Attachment::create(['message_id' => $message->id, 'media'=> $img]);
                }
            }

            //send attachments of this message
            $attachment = Attachment::where('message_id', $message->id)->get();
            //send new message alert
            broadcast(new MessageSentEvent($message, $request->convo_id, Auth::user(), $attachment))->toOthers();
            
            //update last message sent in the convo 
            Conversation::findOrFail($request->convo_id)->touch();

            return response()->json(['message'=> 'Your message has been sent'], 200);

        }catch(Exception $e){
            return $e->getMessage();
            return response()->json(['error'=> 'Your message could not be sent for an unknown reason'], 400);  
        }
    }

    /**
     * Mark all messages as read from the sender. The user has to be apart of this convo
     * input: convo_id
     */
    public function seen(Request $request){
        try{
            //check user belongs in this convo 
            $inConvo = Conversation::findOrFail($request->convo_id)->where(function ($query) {
                $query->where('wanter_id', Auth::user()->id)
                    ->orWhere('fulfiller_id', Auth::user()->id);
            })->firstOrFail();

            $convo_info = Conversation::find($request->convo_id)->first();
            
            // //find the other user
            if($convo_info->wanter_id != Auth::user()->id){
                $other_user_id = $convo_info->wanter_id;
            }else{
                $other_user_id = $convo_info->fulfiller_id;
            }

            //check if the most recent message is read or not 
            $mostRecentSeen = Message::where(['user_id' => $other_user_id, 'conversation_id'=> $request->convo_id])->latest()->first()->seen;
            
            if(!$mostRecentSeen){
                Message::where(['user_id' => $other_user_id, 'conversation_id'=> $request->convo_id])->update(['seen' => 1]); 
            }

        }catch(Exception $e){
            return $e->getMessage();
            return response()->json(['error'=> 'Your message could not be sent for an unknown reason'], 400);  
        }
    }
}
