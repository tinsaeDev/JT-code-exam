<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index( Post $post)
    {
        return $post->comments;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,Post $post)
    {

        $request->validate( [
                            "text"=>"required|string"
                            ]);



           $comment =  Comment::make( $request->all() );
 

    $post->comments()->saveMany([
        $comment
    ]);

    

    return $comment;
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post,Comment $comment)
    {
        return $comment;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post , Comment $comment)
    {

        return $comment->delete();
    }
}
