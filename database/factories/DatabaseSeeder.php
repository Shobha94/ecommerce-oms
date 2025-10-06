<?php

namespace Database\Seeders;

use App\Models\{User, Category, Product, Cart, Order, Payment};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $admins = User::factory()->count(2)->admin()->sequence(
            ['email' => 'admin1@example.com'],
            ['email' => 'admin2@example.com'],
        )->create();

        $customers = User::factory()->count(10)->create();

        // Categories & products
        $categories = Category::factory()->count(5)->create();
        $products   = Product::factory()->count(20)->create();

        // Carts
        foreach ($customers as $u) {
            Cart::query()->insert(
                $products->random(3)->map(fn($p) => [
                    'user_id' => $u->id,
                    'product_id' => $p->id,
                    'quantity' => rand(1, 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray()
            );
        }

        // Orders + Payments (15)
        for ($i=0; $i<15; $i++) {
            $user = $customers->random();
            $order = $user->orders()->create([
                'status' => 'confirmed',
                'total_amount' => 0,
            ]);

            $lineItems = $products->random(rand(1,3));
            $total = 0;
            foreach ($lineItems as $p) {
                $qty = rand(1,2);
                $order->products()->attach($p->id, ['quantity'=>$qty, 'price'=>$p->price]);
                $total += $p->price * $qty;
            }
            $order->update(['total_amount' => $total]);

            $order->payments()->create([
                'amount' => $total,
                'status' => 'success',
                'meta' => ['seeded' => true],
            ]);
        }
    }
}
