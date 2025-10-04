<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_book_ticket(): void
    {
        $customer = User::factory()->customer()->create();
        $ticket = Ticket::factory()->create(['quantity' => 100]);

        $token = $customer->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/tickets/{$ticket->id}/bookings", [
            'quantity' => 2,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'quantity', 'status']
            ]);
    }

    public function test_customer_cannot_book_more_than_available(): void
    {
        $customer = User::factory()->customer()->create();
        $ticket = Ticket::factory()->create(['quantity' => 5]);

        $token = $customer->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/tickets/{$ticket->id}/bookings", [
            'quantity' => 10,
        ]);

        $response->assertStatus(422);
    }

    public function test_customer_can_view_their_bookings(): void
    {
        $customer = User::factory()->customer()->create();
        $booking = Booking::factory()->create(['user_id' => $customer->id]);

        $token = $customer->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/bookings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => ['id', 'quantity', 'status']
                    ]
                ]
            ]);
    }
}