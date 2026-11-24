<?php

namespace Database\Factories;

use App\Models\FilterPreset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FilterPreset>
 */
class FilterPresetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->words(2, true),
            'filters' => [
                'category' => 'Ogólne',
                'sort' => 'latest',
            ],
        ];
    }
}
