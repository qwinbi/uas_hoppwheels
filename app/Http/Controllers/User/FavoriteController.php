<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::with('product')
            ->where('user_id', auth()->id())
            ->get();
            
        return view('user.favorites.index', compact('favorites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $existing = Favorite::where('user_id', auth()->id())
            ->where('product_id', $validated['product_id'])
            ->first();
            
        if ($existing) {
            $existing->delete();
            return redirect()->back()->with('success', 'Produk dihapus dari favorit.');
        }

        Favorite::create([
            'user_id' => auth()->id(),
            'product_id' => $validated['product_id']
        ]);

        return redirect()->back()->with('success', 'Produk ditambahkan ke favorit.');
    }

    public function destroy(Favorite $favorite)
    {
        if ($favorite->user_id !== auth()->id()) {
            abort(403);
        }
        
        $favorite->delete();

        return redirect()->route('favorites.index')->with('success', 'Produk dihapus dari favorit.');
    }
}