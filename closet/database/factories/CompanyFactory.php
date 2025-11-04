<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                        'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->company,
            'cnpj' => $this->faker->numerify('##############'),
            'email' => $this->faker->unique()->companyEmail,
            'password' => bcrypt('password'), // password
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'zip_code' => $this->faker->postcode,
            'description' => $this->faker->paragraph,
            'website' => $this->faker->url,
            'logo' => $this->faker->imageUrl(),
            'is_active' => $this->faker->boolean,
        ];
    }
}
