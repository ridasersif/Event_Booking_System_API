<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'date' => $this->faker->dateTimeBetween('+1 week', '+1 year'),
            'location' => $this->faker->city() . ', ' . $this->faker->country(),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}