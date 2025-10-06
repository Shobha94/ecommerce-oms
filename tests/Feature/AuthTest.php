<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_and_login_and_me_and_logout(): void
    {
        $r = $this->postJson('/api/register', [
            'name'=>'A', 'email'=>'a@example.com', 'password'=>'secret123', 'role'=>'customer'
        ])->assertCreated();

        $token = $r->json('token');

        $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/me')->assertOk()->assertJsonPath('email', 'a@example.com');

        $this->postJson('/api/logout', [], ['Authorization'=>"Bearer $token"])
            ->assertOk();
    }
}
