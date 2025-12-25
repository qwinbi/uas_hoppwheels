<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->get();
            
        $total = $carts->sum('total_price');
        
        return response()->json([
            'success' => true,
            'data' => $carts,
            'total' => $total
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date'
        ]);

        $product = Product::findOrFail($validated['product_id']);
        
        if ($product->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Mobil tidak tersedia untuk disewa.'
            ], 400);
        }

        $start = new \DateTime($validated['start_date']);
        $end = new \DateTime($validated['end_date']);
        $days = $start->diff($end)->days;
        
        if ($days < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal sewa 1 hari.'
            ], 400);
        }

        $totalPrice = $days * $product->price_per_day;

        $cart = Cart::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days' => $days,
            'total_price' => $totalPrice
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mobil berhasil ditambahkan ke keranjang.',
            'data' => $cart->load('product')
        ]);
    }

    public function destroy(Cart $cart, Request $request)
    {
        if ($cart->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang.'
        ]);
    }
}