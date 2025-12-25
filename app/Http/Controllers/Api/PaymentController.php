<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with('order')
            ->whereHas('order', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    public function show(Payment $payment, Request $request)
    {
        if ($payment->order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $payment->load('order');
        
        return response()->json([
            'success' => true,
            'data' => $payment
        ]);
    }

    public function update(Payment $payment, Request $request)
    {
        if ($payment->order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:pending,success,failed'
        ]);

        $payment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui.',
            'data' => $payment
        ]);
    }
}