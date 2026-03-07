<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_and_receive_token()
    {
        $response = $this->postJson('/api/register', [
            'username' => 'testuser',
            'email'    => 'test@example.com',
            'phone'    => '08123456789',
            'password' => 'password123',
            'pin'      => '123456',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['success', 'message', 'data' => ['token']]);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertDatabaseHas('wallets', ['balance' => 0]);
    }

    /** @test */
    public function user_can_login_and_receive_token()
    {
        $user = User::factory()->create(['password' => bcrypt('secret')]);
        $response = $this->postJson('/api/login', [
            'identifier' => $user->email, // LoginRequest uses 'identifier' (email or phone)
            'password'   => 'secret',
        ]);
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'message', 'data' => ['token']]);
    }
}
