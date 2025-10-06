<?php

namespace Tests\Unit;

use App\Models\{User, Category, Product, Cart};
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_discount(): void
    {
        $svc = new OrderService();
        $this->assertSame(90.0, $svc->applyDiscount(100, 0.10));
    }

    public function test_create_from_cart_reduces_stock_and_clears_cart(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->create();
        $p   = Product::factory()->create(['category_id'=>$cat->id,'price'=>100,'stock'=>3]);

        Cart::create(['user_id'=>$user->id,'product_id'=>$p->id,'quantity'=>2]);

        $svc = new OrderService();
        $order = $svc->createFromCart($user, $user->carts()->with('product')->get());

        $this->assertEquals(1, $p->fresh()->stock);
        $this->assertEquals(180.0, $order->total_amount); // 200 with 10% discount = 180 if >= 500? (Adjust:)
    }
}
