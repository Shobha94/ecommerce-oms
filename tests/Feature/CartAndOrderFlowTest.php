<?php

namespace Tests\Feature;

use App\Models\{User, Category, Product};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartAndOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_add_to_cart_and_place_order(): void
    {
        $user = User::factory()->create(['role'=>'customer']);
        $token = $user->createToken('auth')->plainTextToken;

        $cat = Category::factory()->create();
        $product = Product::factory()->create(['category_id'=>$cat->id,'stock'=>5,'price'=>200]);

        $this->withHeader('Authorization',"Bearer $token")
            ->postJson('/api/cart', ['product_id'=>$product->id,'quantity'=>2])
            ->assertCreated();

        $this->postJson('/api/orders', [], ['Authorization'=>"Bearer $token"])
            ->assertCreated()
            ->assertJsonPath('products.0.pivot.quantity', 2);
    }
}
