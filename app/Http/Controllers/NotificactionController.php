<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class NotificactionController extends Controller
{
    /**Get all the unread messages for this user */
    public function get_all_unread(){
        $read_notfications = User::findOrFail(Auth::user()->id)->readNotifications->take(6);
        $notification = [];
        $notification['unread'] = User::findOrFail(Auth::user()->id)->unreadNotifications;
        $notification['read'] = $read_notfications;
        return $notification;
    }


    /**Mark a message as read */
    public function markAsRead(){
        return User::findOrFail(Auth::user()->id)->unreadNotifications->markAsRead();
    }
}
