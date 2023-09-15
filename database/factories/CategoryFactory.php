<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'photo' => "img_product/a79cb9659946ba88c717ebe4aea50e6df62b36e1b1b64cb53efdaf5d06104f8b.jpg",
            'summary' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
