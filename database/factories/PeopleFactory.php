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
            'people_type_id' => PeopleType::inRandomOrder()->first()?->id ?? PeopleType::factory(),
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

    public function phd()
    {
        return $this->state(function (array $attributes) {
            return [
                'people_type_id' => PeopleType::firstOrCreate(['name' => PeopleType::PHD]),
            ];
        });
    }

    public function academic()
    {
        return $this->state(function (array $attributes) {
            return [
                'people_type_id' => PeopleType::firstOrCreate(['name' => PeopleType::ACADEMIC]),
            ];
        });
    }

    public function mpa()
    {
        return $this->state(function (array $attributes) {
            return [
                'people_type_id' => PeopleType::firstOrCreate(['name' => PeopleType::MPA]),
            ];
        });
    }

    public function technical()
    {
        return $this->state(function (array $attributes) {
            return [
                'people_type_id' => PeopleType::firstOrCreate(['name' => PeopleType::TECHNICAL]),
            ];
        });
    }
}
