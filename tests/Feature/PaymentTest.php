<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_process_payment(): void
    {
        $customer = User::factory()->customer()->create();
        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'status' => 'pending'
        ]);

        $token = $customer->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/bookings/{$booking->id}/payment");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'amount', 'status']
            ]);
    }
}