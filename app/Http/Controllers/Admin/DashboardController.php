<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Rating;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 'user')->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');
        
        $recentOrders = Order::with('user', 'product')->latest()->take(5)->get();
        $recentRatings = Rating::with('user', 'product')->latest()->take(5)->get();
        
        return view('admin.dashboard', compact(
            'totalProducts', 
            'totalUsers', 
            'totalOrders', 
            'totalRevenue',
            'recentOrders',
            'recentRatings'
        ));
    }
}