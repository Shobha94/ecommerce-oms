<?php

namespace App\Http\Controllers;

use App\Models\{Cart, Order, Product};
use App\Services\OrderService;
use App\Notifications\OrderPlaced;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $svc) {}

    public function index(Request $r) {
        $orders = Order::with(['products','payments'])
            ->where('user_id', $r->user()->id)
            ->orderByDesc('id')
            ->paginate(10);
        return response()->json($orders);
    }

    public function store(Request $r) {
        $user = $r->user();
        $cart = Cart::with('product')->where('user_id', $user->id)->get();
        if ($cart->isEmpty()) return response()->json(['message'=>'Cart is empty'], 422);

        $order = $this->svc->createFromCart($user, $cart);

        // Notify (queued)
        $user->notify((new OrderPlaced($order))->delay(now()->addSeconds(1)));

        return response()->json($order->load('products'), 201);
    }

    public function updateStatus(Request $r, int $id) {
        $order = Order::findOrFail($id);
        $data = $r->validate(['status'=>'required|in:pending,confirmed,shipped,delivered,cancelled']);
        $order->update(['status'=>$data['status']]);
        return response()->json($order);
    }
}
