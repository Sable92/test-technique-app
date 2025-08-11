<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Infrastructure\Model\Administrator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Génère des admins de test
 */
class AdministratorFactory extends Factory
{
    protected $model = Administrator::class;


    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }


    public function withCredentials(string $email, string $password): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }
}
