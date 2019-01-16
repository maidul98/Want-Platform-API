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


Broadcast::channel('conversation.{convo_id}', function ($user, $convo_id) {

    return Conversation::findOrFail($convo_id)->where('wanter_id', $user->id)->
    orWhere('fulfiller_id', $user->id);

});

