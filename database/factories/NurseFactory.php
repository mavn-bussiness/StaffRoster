<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class NurseFactory extends Factory
{
    public function definition(): array
    {
        $unitId = Unit::factory()->create()->id;

        return [
            'name' => fake()->name(),
            'grade' => fake()->randomElement(['Staff Nurse', 'Senior Nurse', null]),
            'employment_type' => fake()->randomElement(['permanent', 'contract', null]),
            'home_unit_id' => $unitId,
            'current_unit_id' => $unitId,
            'date_joined' => fake()->optional()->date(),
            'status' => 'active',
        ];
    }
}
