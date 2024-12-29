<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalCenter>
 */
class MedicalCenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'fax' => $this->faker->phoneNumber,
            'whatsapp' => $this->faker->phoneNumber,
            'city_id' => City::factory()
        ];
    }
}
