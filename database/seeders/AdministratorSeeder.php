<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Model\Administrator;

class AdministratorSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin1
        Administrator::factory()
            ->withCredentials('admin@test.com', 'password123')
            ->create();

        // 2. Admin2
        Administrator::factory()
            ->withCredentials('dev@test.com', 'dev123')
            ->create();

        // 3. admins aléatoires
        Administrator::factory(3)->create();
    }
}
