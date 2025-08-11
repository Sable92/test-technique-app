<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Infrastructure\Model\Administrator;
use App\Infrastructure\Model\Comment;
use App\Infrastructure\Model\Profile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProfileTest extends TestCase
{
    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $fillable = [
            'first_name',
            'last_name',
            'image_path',
            'status',
            'administrator_id'
        ];

        $profile = new Profile();

        $this->assertSame($fillable, $profile->getFillable());
    }

    #[Test]
    public function it_casts_status_to_string(): void
    {
        $profile = new Profile();

        $this->assertSame('string', $profile->getCasts()['status']);
    }

    #[Test]
    public function it_belongs_to_administrator(): void
    {
        $administrator = $this->createAdministrator();
        $profile = $this->createProfile(['administrator_id' => $administrator->getKey()]);

        $this->assertInstanceOf(Administrator::class, $profile->administrator);

        // compare avec getKey() pour éviter l'accès à la propriété dynamique ->id
        $this->assertSame($administrator->getKey(), $profile->administrator->getKey());
    }

    #[Test]
    public function it_has_many_comments(): void
    {
        $profile = $this->createProfile();
        $comment1 = $this->createComment(['profile_id' => $profile->getKey()]);
        $comment2 = $this->createComment(['profile_id' => $profile->getKey()]);

        $comments = $profile->comments;

        $this->assertInstanceOf(Collection::class, $comments);
        $this->assertCount(2, $comments);
        $this->assertTrue($comments->contains($comment1));
        $this->assertTrue($comments->contains($comment2));
    }

    #[Test]
    public function scope_active_filters_active_profiles(): void
    {
        $activeProfile = $this->createProfile(['status' => 'active']);
        $this->createProfile(['status' => 'pending']);
        $this->createProfile(['status' => 'inactive']);

        $activeProfiles = Profile::active()->get();

        $this->assertCount(1, $activeProfiles);
        $this->assertTrue($activeProfiles->contains($activeProfile));
    }

    #[Test]
    public function scope_with_administrator_loads_administrator_relation(): void
    {
        $administrator = $this->createAdministrator();
        $this->createProfile(['administrator_id' => $administrator->getKey()]);

        $profileWithAdmin = Profile::withAdministrator()->firstOrFail();

        $this->assertSame($administrator->getKey(), $profileWithAdmin->administrator->getKey());
    }

    #[Test]
    public function scope_latest_orders_by_created_at_desc(): void
    {
        $firstProfile = $this->createProfile(['created_at' => now()->subDay()]);
        $secondProfile = $this->createProfile(['created_at' => now()]);

        $profiles = Profile::latest()->get();

        $first = $profiles->first();
        $last = $profiles->last();

        $this->assertNotNull($first);
        $this->assertNotNull($last);

        $this->assertInstanceOf(Profile::class, $first);
        $this->assertInstanceOf(Profile::class, $last);

        // on compare les clés via getKey() pour éviter property.notFound
        $this->assertSame($secondProfile->getKey(), $first->getKey());
        $this->assertSame($firstProfile->getKey(), $last->getKey());
    }

    #[Test]
    public function get_image_url_attribute_returns_storage_url_when_image_path_exists(): void
    {
        Storage::fake('public');

        $profile = $this->createProfile(['image_path' => 'profiles/test.jpg']);

        $expectedUrl = Storage::disk('public')->url('profiles/test.jpg');

        $this->assertSame($expectedUrl, $profile->image_url);
    }

    #[Test]
    public function get_image_url_attribute_returns_null_when_no_image_path(): void
    {
        $profile = $this->createProfile(['image_path' => null]);

        $this->assertNull($profile->image_url);
    }

    #[Test]
    public function delete_image_removes_file_when_exists(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('profiles/test.jpg', 'fake content');

        $profile = $this->createProfile(['image_path' => 'profiles/test.jpg']);

        $this->assertTrue($profile->deleteImage());
        Storage::disk('public')->assertMissing('profiles/test.jpg');
    }

    #[Test]
    public function delete_image_returns_true_when_no_image_path(): void
    {
        $profile = $this->createProfile(['image_path' => null]);

        $this->assertTrue($profile->deleteImage());
    }

    #[Test]
    public function delete_image_returns_true_when_file_does_not_exist(): void
    {
        Storage::fake('public');

        $profile = $this->createProfile(['image_path' => 'profiles/nonexistent.jpg']);

        $this->assertTrue($profile->deleteImage());
    }
}
