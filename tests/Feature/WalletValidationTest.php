<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Wallet;
use Laravel\Sanctum\Sanctum;

class WalletValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'password' => bcrypt('password123'),
            'pin'      => bcrypt('123456'),
        ]);

        Wallet::create(['user_id' => $this->user->id, 'balance' => 500000]);
    }

    // ─── TOP-UP VALIDATION TESTS ────────────────────────────────────────────

    /** @test */
    public function topup_requires_amount()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/topup', ['amount' => '']);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Nominal tidak boleh kosong.']);
    }

    /** @test */
    public function topup_rejects_non_integer_amount()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/topup', ['amount' => 'abc']);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Nominal harus berupa angka.']);
    }

    /** @test */
    public function topup_rejects_symbol_input()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/topup', ['amount' => '1.5#']);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Nominal harus berupa angka.']);
    }

    /** @test */
    public function topup_rejects_negative_amount()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/topup', ['amount' => -100]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Nominal tidak boleh negatif atau nol.']);
    }

    /** @test */
    public function topup_rejects_zero_amount()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/topup', ['amount' => 0]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Nominal tidak boleh negatif atau nol.']);
    }

    /** @test */
    public function topup_rejects_amount_exceeding_max()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/topup', ['amount' => 100000000]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Nominal melebihi batas maksimum transaksi.']);
    }

    // ─── TRANSFER VALIDATION TESTS ──────────────────────────────────────────

    /** @test */
    public function transfer_rejects_empty_amount()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/transfer', [
            'identifier' => 'another@example.com',
            'amount'     => '',
            'pin'        => '123456',
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Nominal tidak boleh kosong.']);
    }

    /** @test */
    public function transfer_rejects_letter_as_amount()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/transfer', [
            'identifier' => 'another@example.com',
            'amount'     => 'abc',
            'pin'        => '123456',
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Nominal harus berupa angka.']);
    }

    /** @test */
    public function transfer_rejects_amount_exceeding_max()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/transfer', [
            'identifier' => 'another@example.com',
            'amount'     => 99999999,
            'pin'        => '123456',
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Nominal melebihi batas maksimum transaksi.']);
    }

    // ─── AUTH / SECURITY TESTS ──────────────────────────────────────────────

    /** @test */
    public function unauthenticated_user_cannot_access_transactions()
    {
        $response = $this->getJson('/api/transactions');

        $response->assertStatus(401);
    }

    /** @test */
    public function unauthenticated_user_cannot_transfer()
    {
        $response = $this->postJson('/api/transfer', [
            'identifier' => 'someone@example.com',
            'amount'     => 10000,
            'pin'        => '123456',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_only_see_own_transactions()
    {
        $anotherUser = User::factory()->create();
        Wallet::create(['user_id' => $anotherUser->id, 'balance' => 0]);

        // Create a transaction for another user
        \App\Models\Transaction::create([
            'user_id'      => $anotherUser->id,
            'type'         => 'topup',
            'amount'       => 100000,
            'reference_id' => 'TEST_OTHER_TXN',
            'status'       => 'success',
        ]);

        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/transactions');

        $response->assertStatus(200);

        // The authenticated user should see 0 transaction (belongs to another user)
        $data = $response->json('data');
        $this->assertEmpty($data['data'] ?? $data);
    }

    /** @test */
    public function transfer_returns_400_when_pin_is_wrong()
    {
        $receiver = User::factory()->create();
        Wallet::create(['user_id' => $receiver->id, 'balance' => 0]);

        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/transfer', [
            'identifier' => $receiver->email,
            'amount'     => 10000,
            'pin'        => '999999', // wrong pin
        ]);

        $response->assertStatus(400)
                 ->assertJsonFragment(['message' => 'PIN yang Anda masukkan salah.']);
    }

    /** @test */
    public function transfer_returns_400_when_balance_insufficient()
    {
        $receiver = User::factory()->create();
        Wallet::create(['user_id' => $receiver->id, 'balance' => 0]);

        Sanctum::actingAs($this->user);

        // Wallet only has 500000 but we try to transfer 1000000
        $response = $this->postJson('/api/transfer', [
            'identifier' => $receiver->email,
            'amount'     => 1000000,
            'pin'        => '123456',
        ]);

        $response->assertStatus(400)
                 ->assertJsonFragment(['message' => 'Saldo tidak cukup']);
    }
}
