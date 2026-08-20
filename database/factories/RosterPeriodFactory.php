<?php

namespace Database\Factories;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RosterPeriodFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('first day of this month', 'first day of next month');

        return [
            'unit_id' => Unit::factory(),
            'created_by' => User::factory(),
            'period_start' => $start->format('Y-m-d'),
            'period_end' => (clone $start)->modify('+27 days')->format('Y-m-d'),
            'status' => 'draft',
        ];
    }
}
