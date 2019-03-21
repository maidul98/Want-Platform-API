<?php
use Illuminate\Support\Facades\Auth;
use App\Conversation;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//listen to chat channel to check for messages
Broadcast::channel('chat.{convo_id}', function ($user, $convo_id) {

    //the user has to be in this chat
    $ful = Conversation::findOrFail($convo_id)->fulfiller_id;
    $want = Conversation::findOrFail($convo_id)->wanter_id;

    if($ful == $user->id || $want == $user->id) return true;

    return false;
});



