<?php

namespace Database\Factories;

use App\Models\Nurse;
use App\Models\RosterPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

class OffDayRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nurse_id' => Nurse::factory(),
            'roster_period_id' => RosterPeriod::factory(),
            'requested_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'reason' => fake()->optional()->sentence(),
            'written_request_date' => fake()->optional()->date(),
            'status' => 'requested',
        ];
    }
}
