<?php

namespace Database\Factories;

use App\Models\People;
use App\Models\PeopleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\People>
 */
class PeopleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'username' => implode('', $this->faker->randomElements(str_split('abcdefghijklmnopqrstuvwxyz'), 3)) . $this->faker->randomNumber(1) . $this->faker->randomLetter(),
            'surname' => $this->faker->lastName(),
            'forenames' => $this->faker->firstName(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'start_at' => now()->subWeeks(rand(10, 20)),
            'end_at' => now()->addYears(rand(1, 3)),
            'type' => collect(PeopleType::values())->random(),
            'group' => collect(['Civil', 'Bio', 'Nano', 'Aero', 'Mech'])->random(),
        ];
    }

    public function arrivingSoon()
    {
        return $this->state(function (array $attributes) {
            return [
                'start_at' => now()->addDays(rand(-7, 14)),
            ];
        });
    }

    public function leavingSoon()
    {
        return $this->state(function (array $attributes) {
            return [
                'end_at' => now()->addDays(rand(-7, 14)),
            ];
        });
    }
}
