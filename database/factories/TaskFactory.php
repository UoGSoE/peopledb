<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'is_optional' => false,
            'is_onboarding' => true,
        ];
    }

    public function leaving()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_onboarding' => false,
            ];
        });
    }

    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    public function optional()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_optional' => true,
            ];
        });
    }
}
