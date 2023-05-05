<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Image;

use Illuminate\Validation\Rules\File;


class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Post $post)
    {



        $comments = $post->comments()->with(["images"])->get();
        return response()->json([
            "data" => $comments
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Post $post)
    {

        $request->validate([
            "text" => "required|string",
            'images.*' => [
                "required",  File::image()
            ],
        ]);



        $comment =  Comment::make($request->all());
        $post->comments()->saveMany([
            $comment
        ]);


        if ($request->has("images") && is_array(  $request->images ) ) {
            foreach ($request->file('images') as $file) {

                $path = $file->store("public");
                $image = new Image();

                $image->path = $path;
                $image->model = get_class($comment);
                $image->model_id = $comment->id;
                $image->save();
            }
        }


        $comments =  $comment->with(["images"])->find($comment->id);
        return response()->json([
            "data" => $comments
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post, Comment $comment)
    {
        $comments = $comment->with(["images"])->find( $comment->id );
        return response()->json([
            "data" => $comments
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post, Comment $comment)
    {
        $request->validate(
            [
                "text" => "string",
                'images.*' => [
                    File::image()
                ],
                
            ]
        );

        $comment->update( $request->all() );
        $comment =  $comment->with(["images"])->find( $comment->id );
        return response()->json([
            "data" => $comment
        ], 200);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post, Comment $comment)
    {
        return $comment->delete();
    }
}
