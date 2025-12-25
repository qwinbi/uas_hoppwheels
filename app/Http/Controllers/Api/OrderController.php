<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('product', 'payment')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function store(Request $request)
    {
        $cartItems = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->get();
            
        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong.'
            ], 400);
        }

        $orders = [];
        
        foreach ($cartItems as $cartItem) {
            if ($cartItem->product->status !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'Mobil ' . $cartItem->product->name . ' tidak tersedia.'
                ], 400);
            }
        }

        foreach ($cartItems as $cartItem) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'product_id' => $cartItem->product_id,
                'start_date' => $cartItem->start_date,
                'end_date' => $cartItem->end_date,
                'days' => $cartItem->days,
                'total_price' => $cartItem->total_price,
                'status' => 'pending'
            ]);

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'virtual_account',
                'va_number' => '888' . str_pad($order->id, 10, '0', STR_PAD_LEFT),
                'amount' => $order->total_price,
                'status' => 'pending'
            ]);

            $cartItem->product->update(['status' => 'rented']);
            
            $orders[] = $order;
        }

        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat.',
            'data' => $orders
        ]);
    }

    public function show(Order $order, Request $request)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $order->load('product', 'payment');
        
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
}