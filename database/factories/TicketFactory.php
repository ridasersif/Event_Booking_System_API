<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        $types = ['VIP', 'Standard', 'Premium', 'General Admission', 'Early Bird'];
        
        return [
            'type' => $this->faker->randomElement($types),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'quantity' => $this->faker->numberBetween(10, 1000),
            'event_id' => \App\Models\Event::factory(),
        ];
    }
}