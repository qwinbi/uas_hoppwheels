<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $orders = Order::with('product')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
            
        $favorites = Favorite::with('product')
            ->where('user_id', $user->id)
            ->get();
            
        $recommendations = Product::where('status', 'available')
            ->inRandomOrder()
            ->take(3)
            ->get();
            
        return view('user.profile.index', compact('user', 'orders', 'favorites', 'recommendations'));
    }
}