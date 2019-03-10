<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bookmark;
use App\Want;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    /**
     * Add a new bookmark for this user
     * Input: want_id
     */
    public function add(Request $request)
    {
        return Bookmark::create(['user_id'=> Auth::user()->id, 'want_id' => $request->want_id]);

        return Want::where('price', '>', 100)->searchable();
    }

    /**
     * Remove the bookmark
     * Input:bookmark_id
     */
    public function remove(Request $request)
    {
        return Bookmark::findOrFail($request->bookmark_id)->where('user_id', Auth::user()->id)->delete();
    }

    /**
     * Get all the bookmarks along with the Want for this user 
     */
    public function all()
    {
        return Bookmark::where('user_id', Auth::user()->id)->with('want')->get();
    }
}
