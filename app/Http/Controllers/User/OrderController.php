<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('product', 'payment')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
            
        return view('user.orders.index', compact('orders'));
    }

    public function checkout(Request $request)
    {
        $cartItems = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();
            
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        foreach ($cartItems as $cartItem) {
            if ($cartItem->product->status !== 'available') {
                return redirect()->route('cart.index')->with('error', 'Mobil ' . $cartItem->product->name . ' tidak tersedia.');
            }
        }

        foreach ($cartItems as $cartItem) {
            $order = Order::create([
                'user_id' => auth()->id(),
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
        }

        Cart::where('user_id', auth()->id())->delete();

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        
        $order->load('product', 'payment');
        return view('user.orders.show', compact('order'));
    }
}