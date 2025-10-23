<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Community extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'cover_image',
        'is_public',
        'members',
        'created_by'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'members' => 'integer',
    ];

    // Renamed to avoid conflict with the members count column
    public function communityMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function admins()
    {
        return $this->communityMembers()->wherePivot('role', 'admin');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
                ->withTimestamps();
    }

        public static function createWithEvents(array $attributes, array $eventIds = []): self
        {
            $community = static::create($attributes);
            if (!empty($eventIds)) {
                $community->events()->attach($eventIds);
            }
            return $community;
        }

        public function deleteWithEvents(): bool
        {
            $this->events()->detach();
            return $this->delete();
        }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Use the slug for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Allow resolving by slug (preferred) or falling back to ID.
     * This lets URLs like /communities/my-slug and /communities/123 both work.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Try slug first (default behavior)
        $bySlug = $this->newQuery()->where('slug', $value)->first();
        if ($bySlug) {
            return $bySlug;
        }

        // Fall back to ID when a numeric value is provided
        if (is_numeric($value)) {
            return $this->newQuery()->where('id', (int) $value)->first();
        }

        return null;
    }
}
