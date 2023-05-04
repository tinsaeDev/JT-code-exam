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
    public function index( Post $post)
    {
        return $post->comments()->with(["images"])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,Post $post)
    {

        $request->validate( [
                            "text"=>"required|string",
                            'images.*' => [                       
                                "required",  File::image()
                             ],
                        ]);



           $comment =  Comment::make( $request->all() );
           $post->comments()->saveMany([
                $comment
            ]);


            foreach(   $request->file('images') as $file  )  {
                
                $path = $file->store("public");
                $image = new Image();
                
                $image->path =$path;
                $image->model=get_class( $comment );
                $image->model_id = $comment->id;
                $image->save();

            }

            return $comment->with(["images"])->find( $comment->id );
      
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
