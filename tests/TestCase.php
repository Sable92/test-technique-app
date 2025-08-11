<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use App\Infrastructure\Model\Administrator;
use App\Infrastructure\Model\Profile;
use App\Infrastructure\Model\Comment;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Utiliser la base de données de test
        $this->artisan('migrate:fresh');
    }

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        /** @var Application $app */
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Créer un administrateur pour les tests
     *
     * @param array<string, mixed> $attributes
     */
    protected function createAdministrator(array $attributes = []): Administrator
    {
        /** @var Administrator $admin */
        $admin = Administrator::factory()->create($attributes);

        return $admin;
    }

    /**
     * Créer un profil pour les tests
     *
     * @param array<string, mixed> $attributes
     */
    protected function createProfile(array $attributes = []): Profile
    {
        /** @var Profile $profile */
        $profile = Profile::factory()->create($attributes);

        return $profile;
    }

    /**
     * Créer un commentaire pour les tests
     *
     * @param array<string, mixed> $attributes
     */
    protected function createComment(array $attributes = []): Comment
    {
        /** @var Comment $comment */
        $comment = Comment::factory()->create($attributes);

        return $comment;
    }
}
