<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Image;

use Illuminate\Http\Request;

use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts =  Post::with(["images","comments"])->get();

        return response()->json([
            "data"=>$posts
        ],200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


            $request->validate(
                [
                    "title"=>"required|string",
                    "content"=>"required|string",
                    "category_id"=>"required|exists:categories,id",
                    'images.*' => [                       
                        "required",  File::image()
                     ],
                ]
            );




            $post = Post::create( $request->all() );

           

            if( $request->has("images") ){
                foreach(   $request->file('images') as $file  )  {
                
                    $path = $file->store("public");
                    $image = new Image();
                    
                    $image->path =$path;
                    $image->model=get_class( $post );
                    $image->model_id = $post->id;
                   $image->save();
    
                }
            }

            

            $post = $post->with(["images"])->find( $post->id );
            return response()->json([
                "data"=>$post
            ],200);



    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post = $post->with(["images","comments"])->find( $post->id );
        return response()->json([
            "data"=>$post
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {

        $request->validate(
                [
                    "title"=>"string",
                    "content"=>"string",
                    "category_id"=>"exists:categories,id",
                    
                ]
            );

            $post->update( $request->all() );
            return $post;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        return $post->delete();
    }
}
