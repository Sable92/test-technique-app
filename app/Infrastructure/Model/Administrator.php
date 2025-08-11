<?php

declare(strict_types=1);

namespace App\Infrastructure\Model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\AdministratorFactory;

/**
 * @property int $id
 * @property string|null $email
 * @property string $password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Profile> $profiles
 * @property-read Collection<int, Comment> $comments
 *
 * @method static AdministratorFactory factory()
 */
class Administrator extends Authenticatable
{
    /** @use HasFactory<AdministratorFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Relation avec les profils
     *
     * @return HasMany<Profile, $this>
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    /**
     * Relation avec les commentaires
     *
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Lien vers la Factory
     *
     * @return AdministratorFactory
     */
    protected static function newFactory(): AdministratorFactory
    {
        return AdministratorFactory::new();
    }
}
