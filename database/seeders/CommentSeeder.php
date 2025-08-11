<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Model\Administrator;
use App\Infrastructure\Model\Profile;
use App\Infrastructure\Model\Comment;

class CommentSeeder extends Seeder
{
    /**
     * Créer des commentaires de test
     */
    public function run(): void
    {
        // Récupère des administrateurs et profils existants
        $administrators = Administrator::limit(3)->get();
        $profiles = Profile::limit(5)->get();

        if ($administrators->isEmpty()) {
            $this->command->error('Aucun administrateur trouvé. Exécuter d\'abord AdministratorSeeder.');
            return;
        }

        if ($profiles->isEmpty()) {
            $this->command->error('Aucun profil trouvé. Exécuter d\'abord ProfileSeeder.');
            return;
        }

        // 1. Commentaire pour les premier profil
        $firstProfile = $profiles->first();

        Comment::factory()
            ->byAdministrator($administrators->first())
            ->forProfile($firstProfile)
            ->withContent('Excellent profil ! Très professionnel.')
            ->create();

        Comment::factory()
            ->byAdministrator($administrators->get(1))
            ->forProfile($firstProfile)
            ->withContent('Je recommande vivement cette personne.')
            ->create();

        // 2. Commentaires aléatoires pour les autres profils
        foreach ($profiles as $profile) {
            // 1 à 3 commentaires par profil
            $commentCount = rand(1, 3);

            for ($i = 0; $i < $commentCount; $i++) {
                $randomAdmin = $administrators->random();

                Comment::factory()
                    ->byAdministrator($randomAdmin)
                    ->forProfile($profile)
                    ->create();
            }
        }

        // 3. commentaires longs
        Comment::factory(2)
            ->long()
            ->create();

        // 4. commentaires courts
        Comment::factory(3)
            ->short()
            ->create();

        $this->command->info('Commentaires créés avec succès !');
    }
}
