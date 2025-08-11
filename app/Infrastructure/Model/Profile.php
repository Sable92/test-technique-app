<?php

declare(strict_types=1);

namespace App\Infrastructure\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Database\Factories\ProfileFactory;

/**
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $image_path
 * @property string $status
 * @property int $administrator_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Administrator $administrator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read string|null $image_url
 *
 * @method static Builder<static> active()
 * @method static Builder<static> withAdministrator()
 * @method static Builder<static> latest()
 * @method static ProfileFactory factory()
 */
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'image_path',
        'status',
        'administrator_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'string',
            'administrator_id' => 'integer',
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
     * Relation avec les commentaires
     *
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Scope pour récupérer seulement les profils actifs
     *
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour récupérer les profils avec administrateur
     *
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeWithAdministrator(Builder $query): Builder
    {
        return $query->with(['administrator']);
    }

    /**
     * Scope pour ordonner par date de création (plus récent en premier)
     *
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Supprime l'image du profil du stockage
     */
    public function deleteImage(): bool
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->delete($this->image_path);
        }
        return true;
    }

    /**
     * Accessor pour l'URL de l'image
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    /**
     * Lien vers la Factory
     *
     * @return ProfileFactory
     */
    protected static function newFactory(): ProfileFactory
    {
        return ProfileFactory::new();
    }
}
