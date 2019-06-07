<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class NotificactionController extends Controller
{
    /**Get all the unread messages for this user */
    public function get_all_unread(){
        // $push_notifications = User::findOrFail(Auth::user()->id)->notifications->take(8)->get()->put('past_notifactions', $push_notifications);
        return User::findOrFail(Auth::user()->id)->unreadNotifications;
    }


    /**Mark a message as read */
    public function markAsRead(){
        return User::findOrFail(Auth::user()->id)->unreadNotifications->markAsRead();
    }
}
