<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $categories = Category::get();
          return response()->json([
            "data"=>$categories
        ],200);
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            "name"=>"string|required|max:25"
        ]);


        $Category = Category::create( $request->all() );
        
        return response()->json([
            "data"=>$Category
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {

        return response()->json([
            "data"=>$category
        ],200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {

        $request->validate([
            "name"=>"string"
        ]);

     
        $category->update( $request->all() );
        return response()->json([
            "data"=>$category
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        
        return $category->delete();
    
    }
}
