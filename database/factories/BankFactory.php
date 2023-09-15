<?php

namespace Database\Factories;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bank>
 * Class BankFactory
 *
 * @package Database\Factories
 *
 * @method \App\Models\Bank createOne(array $attributes = [])
 * @method \App\Models\Bank[] createMany(int $times = 1, array|callable $attributes = [])
 */
class BankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $path = public_path('json/bank.json');
        $data = json_decode(file_get_contents($path), true);
        $banks = array_column($data, 'bank_code');
        return [
            'bank_name' => $banks[array_rand($banks, 1)],
            'branch_name' => 'Jakarta',
            'account_name' => $this->faker->name(),
            'account_number' => $this->faker->creditCardNumber(),
            'status' => 'active',
        ];
    }
}
