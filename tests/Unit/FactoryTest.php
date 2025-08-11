<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Infrastructure\Model\Administrator;
use App\Infrastructure\Model\Profile;

class FactoryTest extends TestCase
{
    public function test_can_create_administrator(): void
    {
        $admin = Administrator::factory()->create();
        $this->assertNotNull($admin->getKey());
    }

    public function test_can_create_profile(): void
    {
        $profile = Profile::factory()->create();
        $this->assertNotNull($profile->getKey());
    }
}
