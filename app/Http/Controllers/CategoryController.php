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

        return Category::get();
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            "name"=>"string|required"
        ]);


        $Category = Category::create( $request->all() );
        return $Category;
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
     return $category;
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
        return $category;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {

        $category->delete();
        return response()->json([
            "message"=>"Resource deleted"
        ]);
    
    }
}
