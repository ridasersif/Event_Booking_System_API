<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_events_list(): void
    {
        Event::factory()->count(3)->create();

        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => ['id', 'title', 'description', 'date', 'location']
                    ]
                ]
            ]);
    }

    public function test_organizer_can_create_event(): void
    {
        $organizer = User::factory()->organizer()->create();
        $token = $organizer->createToken('test-token')->plainTextToken;

        $eventData = [
            'title' => 'Test Event',
            'description' => 'Test Event Description',
            'date' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'location' => 'Test Location',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/events', $eventData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'title', 'description', 'date', 'location']
            ]);
    }

    public function test_customer_cannot_create_event(): void
    {
        $customer = User::factory()->customer()->create();
        $token = $customer->createToken('test-token')->plainTextToken;

        $eventData = [
            'title' => 'Test Event',
            'description' => 'Test Event Description',
            'date' => now()->addDays(7),
            'location' => 'Test Location',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/events', $eventData);

        $response->assertStatus(403);
    }
}