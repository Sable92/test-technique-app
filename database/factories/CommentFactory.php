<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Infrastructure\Model\Comment;
use App\Infrastructure\Model\Administrator;
use App\Infrastructure\Model\Profile;

/**
 * Génère des commentaires de test
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;


    public function definition(): array
    {
        return [
            'content' => fake()->paragraph(3),
            'administrator_id' => Administrator::factory(),
            'profile_id' => Profile::factory(),
        ];
    }

    /**
     * État : commentaire court
     */
    public function short(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->sentence(),
        ]);
    }

    /**
     * État : commentaire long
     */
    public function long(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->paragraphs(5, true), // 5 paragraphes
        ]);
    }

    /**
     * État : commentaire pour un administrateur spécifique
     */
    public function byAdministrator(Administrator $administrator): static
    {
        return $this->state(fn (array $attributes) => [
            'administrator_id' => $administrator->id,
        ]);
    }

    /**
     * État : commentaire pour un profil spécifique
     */
    public function forProfile(Profile $profile): static
    {
        return $this->state(fn (array $attributes) => [
            'profile_id' => $profile->id,
        ]);
    }

    /**
     * État : commentaire avec contenu spécifique
     */
    public function withContent(string $content): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => $content,
        ]);
    }
}
