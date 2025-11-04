<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
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
            'company_id' => \App\Models\Company::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'type' => $this->faker->randomElement(['desconto_percentual', 'desconto_valor', 'frete_gratis', 'brinde', 'outro']),
            'discount_percentage' => $this->faker->numberBetween(5, 50),
            'discount_amount' => $this->faker->randomFloat(2, 5, 100),
            'minimum_purchase' => $this->faker->randomFloat(2, 10, 200),
            'coupon_code' => $this->faker->unique()->word,
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'is_active' => $this->faker->boolean,
            'image' => $this->faker->imageUrl(),
            'terms_conditions' => $this->faker->text,
        ];
    }
}
