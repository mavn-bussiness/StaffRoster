<?php

namespace Database\Factories;

use App\Models\Nurse;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveRequestFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('now', '+1 month');

        return [
            'nurse_id' => Nurse::factory(),
            'type' => fake()->randomElement(['annual', 'sick', 'maternity', 'compassionate', 'other']),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => (clone $start)->modify('+7 days')->format('Y-m-d'),
            'return_date' => null,
            'status' => 'pending',
            'approved_by' => null,
        ];
    }
}
