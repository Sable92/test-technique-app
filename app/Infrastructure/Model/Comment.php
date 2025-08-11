<?php

declare(strict_types=1);

namespace App\Infrastructure\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Database\Factories\CommentFactory;
use Illuminate\Support\Carbon;

/**
 * Modèle Comment
 * Commentaire sur un profil, écrit par un administrateur
 *
 * @property int $id
 * @property string $content
 * @property int $administrator_id
 * @property int $profile_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Administrator $administrator
 * @property-read Profile $profile
 *
 * @method static CommentFactory factory()
 */
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'content',
        'administrator_id',
        'profile_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'administrator_id' => 'integer',
            'profile_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relation avec l'administrateur
     *
     * @return BelongsTo<Administrator, $this>
     */
    public function administrator(): BelongsTo
    {
        return $this->belongsTo(Administrator::class);
    }

    /**
     * Relation avec le profil
     *
     * @return BelongsTo<Profile, $this>
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Lien vers la Factory
     *
     * @return CommentFactory
     */
    protected static function newFactory(): CommentFactory
    {
        return CommentFactory::new();
    }
}
