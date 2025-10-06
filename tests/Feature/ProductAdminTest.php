<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create(['role'=>'admin']);
        $token = $admin->createToken('auth')->plainTextToken;

        $cat = \App\Models\Category::factory()->create();

        $this->withHeader('Authorization',"Bearer $token")
            ->postJson('/api/products', [
                'name'=>'P1','price'=>100,'stock'=>10,'category_id'=>$cat->id
            ])->assertCreated()->assertJsonPath('name', 'P1');
    }
}
