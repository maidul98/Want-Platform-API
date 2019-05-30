<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Want;
use App\Comment;

class CommentController extends Controller
{
    /**
     * Create a new comment
     * Input: comment_body, want_id
     */
    public function store(Request $request)
    {
        $comment = new Comment;
        $comment->body = $request->get('comment_body');
        $comment->user()->associate($request->user());
        $post = Want::find($request->get('want_id'));
        $post->comments()->save($comment);

    }

    /**
     * Create a new reply
     * Input:comment_body, comment_id, want_id
     */
    public function replyStore(Request $request)
    {
        $reply = new Comment();
        $reply->body = $request->get('comment_body');
        $reply->user()->associate($request->user());
        $reply->parent_id = $request->get('comment_id');
        $post = Want::find($request->get('want_id'));
        $post->comments()->save($reply);
    }

    /**
     * Delete a comment owned by this user or delete any comment if the user is the owner of this post 
     * Input: want_id, comment_id
     */
    public function deleteComment($comment_id, $want_id){
        $want = Want::find($want_id)->findOrFail();
    }
}
