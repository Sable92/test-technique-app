<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Infrastructure\Model\Comment;
use App\Infrastructure\Model\Profile;
use App\Infrastructure\Model\Administrator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommentTest extends TestCase
{
    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $fillable = [
            'content',
            'administrator_id',
            'profile_id'
        ];

        $comment = new Comment();

        $this->assertEquals($fillable, $comment->getFillable());
    }

    #[Test]
    public function it_belongs_to_administrator(): void
    {
        $administrator = $this->createAdministrator();
        $comment = $this->createComment(['administrator_id' => $administrator->id]);

        $this->assertInstanceOf(Administrator::class, $comment->administrator);
        $this->assertEquals($administrator->id, $comment->administrator->id);
    }

    #[Test]
    public function it_belongs_to_profile(): void
    {
        $profile = $this->createProfile();
        $comment = $this->createComment(['profile_id' => $profile->id]);

        $this->assertInstanceOf(Profile::class, $comment->profile);
        $this->assertEquals($profile->id, $comment->profile->id);
    }

    #[Test]
    public function it_can_check_if_administrator_already_commented_on_profile(): void
    {
        $administrator = $this->createAdministrator();
        $profile = $this->createProfile();

        // Pas encore de commentaire
        $exists = Comment::where('administrator_id', $administrator->id)
            ->where('profile_id', $profile->id)
            ->exists();

        $this->assertFalse($exists);

        // Créer un commentaire
        $this->createComment([
            'administrator_id' => $administrator->id,
            'profile_id' => $profile->id
        ]);

        // Déjà commenté
        $exists = Comment::where('administrator_id', $administrator->id)
            ->where('profile_id', $profile->id)
            ->exists();

        $this->assertTrue($exists);
    }

    #[Test]
    public function administrator_can_comment_on_different_profiles(): void
    {
        $administrator = $this->createAdministrator();
        $profile1 = $this->createProfile();
        $profile2 = $this->createProfile();

        $comment1 = $this->createComment([
            'administrator_id' => $administrator->id,
            'profile_id' => $profile1->id
        ]);

        $comment2 = $this->createComment([
            'administrator_id' => $administrator->id,
            'profile_id' => $profile2->id
        ]);

        $this->assertEquals($administrator->id, $comment1->administrator_id);
        $this->assertEquals($administrator->id, $comment2->administrator_id);
        $this->assertEquals($profile1->id, $comment1->profile_id);
        $this->assertEquals($profile2->id, $comment2->profile_id);
    }

    #[Test]
    public function different_administrators_can_comment_on_same_profile(): void
    {
        $administrator1 = $this->createAdministrator();
        $administrator2 = $this->createAdministrator();
        $profile = $this->createProfile();

        $comment1 = $this->createComment([
            'administrator_id' => $administrator1->id,
            'profile_id' => $profile->id
        ]);

        $comment2 = $this->createComment([
            'administrator_id' => $administrator2->id,
            'profile_id' => $profile->id
        ]);

        $this->assertEquals($profile->id, $comment1->profile_id);
        $this->assertEquals($profile->id, $comment2->profile_id);
        $this->assertEquals($administrator1->id, $comment1->administrator_id);
        $this->assertEquals($administrator2->id, $comment2->administrator_id);
    }
}
