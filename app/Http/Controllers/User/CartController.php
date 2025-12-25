<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')->where('user_id', auth()->id())->get();
        $total = $carts->sum('total_price');
        
        return view('user.cart.index', compact('carts', 'total'));
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
            return redirect()->back()->with('error', 'Mobil tidak tersedia untuk disewa.');
        }

        $start = new \DateTime($validated['start_date']);
        $end = new \DateTime($validated['end_date']);
        $days = $start->diff($end)->days;
        
        if ($days < 1) {
            return redirect()->back()->with('error', 'Minimal sewa 1 hari.');
        }

        $totalPrice = $days * $product->price_per_day;

        Cart::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days' => $days,
            'total_price' => $totalPrice
        ]);

        return redirect()->route('cart.index')->with('success', 'Mobil berhasil ditambahkan ke keranjang.');
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }
        
        $cart->delete();

        return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}