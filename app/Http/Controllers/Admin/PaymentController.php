<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('order')->get();
        return view('admin.payments.index', compact('payments'));
    }

    public function edit(Payment $payment)
    {
        return view('admin.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,success,failed',
            'qris_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string'
        ]);

        if ($request->hasFile('qris_image')) {
            if ($payment->qris_image) {
                Storage::disk('public')->delete($payment->qris_image);
            }
            $validated['qris_image'] = $request->file('qris_image')->store('qris', 'public');
        }

        $payment->update($validated);

        return redirect()->route('admin.payments.index')->with('success', 'Pembayaran berhasil diperbarui.');
    }
}