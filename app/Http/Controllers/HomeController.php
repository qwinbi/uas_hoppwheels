<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Logo;
use App\Models\Footer;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('status', 'available')->get();
        $logo = Logo::where('is_active', true)->first();
        $footer = Footer::first();
        
        return view('home', compact('products', 'logo', 'footer'));
    }

    public function about()
    {
        $about = \App\Models\About::first();
        $logo = Logo::where('is_active', true)->first();
        $footer = Footer::first();
        
        return view('about', compact('about', 'logo', 'footer'));
    }
}