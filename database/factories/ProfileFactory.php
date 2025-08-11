<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Infrastructure\Model\Profile;
use App\Infrastructure\Model\Administrator;

/**
 * Génère des profils de test
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    /**
     * État par défaut du modèle
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'image_path' => null, // Pas d'image par défaut pour les tests
            'status' => fake()->randomElement(['inactive', 'pending', 'active']),
            'administrator_id' => Administrator::factory(),
        ];
    }

    /**
     * État : profil actif
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * État : profil inactif
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * État : profil en attente
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * État : avec administrateur spécifique
     */
    public function forAdministrator(Administrator $administrator): static
    {
        return $this->state(fn (array $attributes) => [
            'administrator_id' => $administrator->id,
        ]);
    }
}
