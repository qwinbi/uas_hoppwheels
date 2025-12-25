<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $ratings = Rating::with('user', 'product')
            ->where('user_id', $request->user()->id)
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $ratings
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
            'type' => 'required|in:product,website',
            'product_id' => 'required_if:type,product|exists:products,id'
        ]);

        $rating = Rating::create([
            'user_id' => $request->user()->id,
            'product_id' => $validated['type'] === 'product' ? $validated['product_id'] : null,
            'rating' => $validated['rating'],
            'review' => $validated['review'],
            'type' => $validated['type']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rating berhasil dikirim.',
            'data' => $rating
        ]);
    }
}