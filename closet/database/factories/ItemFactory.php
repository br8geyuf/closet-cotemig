<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'brand' => $this->faker->company(),
            'size' => $this->faker->randomElement(['PP', 'P', 'M', 'G', 'GG']),
            'colors' => [$this->faker->colorName(), $this->faker->colorName()],
            'condition' => $this->faker->randomElement(['novo', 'usado_excelente', 'usado_bom', 'usado_regular']),
            'purchase_price' => $this->faker->randomFloat(2, 10, 500),
            'purchase_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'images' => [
                'https://via.placeholder.com/640x480.png/00ff77?text=' . $this->faker->word(),
                'https://via.placeholder.com/640x480.png/0077ff?text=' . $this->faker->word(),
            ],
            'tags' => [$this->faker->word(), $this->faker->word()],
            'usage_count' => $this->faker->numberBetween(0, 100),
            'last_used' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'is_favorite' => $this->faker->boolean(),
            'season' => $this->faker->randomElement(['verao', 'outono', 'inverno', 'primavera', 'todas']),
            'occasion' => $this->faker->randomElement(['casual', 'trabalho', 'festa', 'esporte', 'formal', 'todas']),
        ];
    }
}

