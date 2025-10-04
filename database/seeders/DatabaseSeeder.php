<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Second Admin',
            'email' => 'admin2@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $organizers = \App\Models\User::factory(3)->organizer()->create();
        $customers = \App\Models\User::factory(10)->customer()->create();

        $events = \App\Models\Event::factory(5)->create([
            'created_by' => fn() => $organizers->random()->id,
        ]);

        $tickets = \App\Models\Ticket::factory(15)->create([
            'event_id' => fn() => $events->random()->id,
        ]);

        \App\Models\Booking::factory(20)->create([
            'user_id' => fn() => $customers->random()->id,
            'ticket_id' => fn() => $tickets->random()->id,
        ]);

        \App\Models\Booking::all()->each(function ($booking) {
            \App\Models\Payment::factory()->create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount,
                'status' => $booking->status === 'confirmed' ? 'success' : 'failed',
            ]);
        });
    }
}