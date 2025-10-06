<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Mock payment
    public function pay(Request $r, int $id) {
        $order = Order::with('payments')->findOrFail($id);
        $this->authorize('view', $order); // optional: policies, or ensure user owns it / admin

        if ($order->payments()->exists()) {
            return response()->json(['message'=>'Payment already recorded'], 422);
        }

        $amount = $order->total_amount;
        $status = $r->input('force_status') ?? (rand(0,9) > 1 ? 'success' : 'failed');

        $payment = $order->payments()->create([
            'amount' => $amount,
            'status' => $status,
            'meta' => ['gateway'=>'mock','txn_id'=>uniqid('txn_')],
        ]);

        if ($status === 'success' && $order->status === 'pending') {
            $order->update(['status' => 'confirmed']);
        }

        return response()->json($payment, 201);
    }

    public function show(int $id) {
        return Payment::with('order')->findOrFail($id);
    }
}
