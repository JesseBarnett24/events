<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    // Retrieve and return all categories as a JSON response
    // @return \Illuminate\Http\JsonResponse
    public function index()
    {
        return response()->json(Category::all());
    }
}
