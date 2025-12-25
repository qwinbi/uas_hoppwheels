<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
            'type' => 'required|in:product,website',
            'product_id' => 'required_if:type,product|exists:products,id'
        ]);

        Rating::create([
            'user_id' => auth()->id(),
            'product_id' => $validated['type'] === 'product' ? $validated['product_id'] : null,
            'rating' => $validated['rating'],
            'review' => $validated['review'],
            'type' => $validated['type']
        ]);

        return redirect()->back()->with('success', 'Rating berhasil dikirim.');
    }
}