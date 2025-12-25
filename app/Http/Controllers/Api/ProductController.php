<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function available()
    {
        $products = Product::where('status', 'available')->get();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}