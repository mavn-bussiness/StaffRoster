<?php

namespace Database\Factories;

use App\Models\Nurse;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nurse_id' => Nurse::factory(),
            'from_unit_id' => Unit::factory(),
            'to_unit_id' => Unit::factory(),
            'start_date' => fake()->date(),
            'end_date' => fake()->optional()->date(),
            'reason' => fake()->optional()->sentence(),
            'status' => 'pending',
        ];
    }
}
