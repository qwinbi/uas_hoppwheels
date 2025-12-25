<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
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
            
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'orders' => $orders,
                'favorites' => $favorites,
                'recommendations' => $recommendations
            ]
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diperbarui.',
            'data' => $user
        ]);
    }
}