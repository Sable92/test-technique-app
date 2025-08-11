<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Model\Administrator;
use App\Infrastructure\Model\Profile;

class ProfileSeeder extends Seeder
{

    public function run(): void
    {
        // Récupérer l'admin de test principal
        $admin = Administrator::where('email', 'admin@test.com')->first();

        if (!$admin) {
            $this->command->error('Administrator avec email admin@test.com non trouvé. Exécutez d\'abord AdministratorSeeder.');
            return;
        }

        // 1. Profils spécifiques avec cet admin
        Profile::factory()
            ->forAdministrator($admin)
            ->active()
            ->create([
                'first_name' => 'Jean',
                'last_name' => 'Dupont'
            ]);

        Profile::factory()
            ->forAdministrator($admin)
            ->pending()
            ->create([
                'first_name' => 'Marie',
                'last_name' => 'Martin'
            ]);

        Profile::factory()
            ->forAdministrator($admin)
            ->inactive()
            ->create([
                'first_name' => 'Pierre',
                'last_name' => 'Bernard'
            ]);

        // 2. Récupérer tous les autres administrators pour répartir les profils
        $administrators = Administrator::all();

        if ($administrators->count() > 1) {
            // 3. Créer des profils aléatoires répartis entre les administrators
            foreach ($administrators as $administrator) {
                // 2-4 profils par administrator
                $profileCount = rand(2, 4);

                Profile::factory($profileCount)
                    ->forAdministrator($administrator)
                    ->create();
            }
        }

        // 4. Quelques profils supplémentaires avec statuts variés
        Profile::factory(3)->active()->create();
        Profile::factory(2)->pending()->create();
        Profile::factory(1)->inactive()->create();

        $this->command->info('Profils créés avec succès !');
    }
}
