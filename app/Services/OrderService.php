<?php

namespace App\Services;

use App\Models\{Order, Product, User, Cart};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createFromCart(User $user, Collection $cartItems): Order
    {
        return DB::transaction(function () use ($user, $cartItems) {
            $order = $user->orders()->create(['status'=>'pending','total_amount'=>0]);
            $total = 0;

            foreach ($cartItems as $item) {
                /** @var \App\Models\Product $product */
                $product = Product::lockForUpdate()->find($item->product_id);

                if ($product->stock < $item->quantity) {
                    abort(409, 'Stock changed during checkout');
                }

                $lineTotal = $product->price * $item->quantity;
                $order->products()->attach($product->id, [
                    'quantity' => $item->quantity,
                    'price'    => $product->price,
                ]);

                $product->decrement('stock', $item->quantity);
                $total += $lineTotal;
            }

            // Apply simple discount example (10% over ₹500)
            if ($total >= 500) {
                $total = $this->applyDiscount($total, 0.10);
            }

            $order->update(['total_amount'=>$total]);

            Cart::where('user_id', $user->id)->delete();

            return $order->fresh(['products']);
        });
    }

    public function applyDiscount(float $amount, float $rate): float
    {
        return round($amount * (1 - $rate), 2);
    }
}
