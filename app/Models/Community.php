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

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role'])
            ->wherePivot('role', 'admin');
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
}
