<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;

class StockAvailabilityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $items = Cart::with('product')->where('user_id', $user->id)->get();

        foreach ($items as $item) {
            if ($item->product->stock < $item->quantity) {
                return response()->json([
                    'message' => 'Insufficient stock',
                    'product_id' => $item->product_id,
                    'available' => $item->product->stock,
                    'requested' => $item->quantity
                ], 409);
            }
        }
        return $next($request);
    }
}
