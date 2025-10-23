<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'organizer_id', 'title', 'description', 'location', 'thumbnail', 'start_date', 'end_date', 'max_participants', 'is_public', 'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(Community::class)
            ->withTimestamps();
    }

        public static function createWithCommunities(array $attributes, array $communityIds = []): self
        {
            $event = static::create($attributes);
            if (!empty($communityIds)) {
                $event->communities()->attach($communityIds);
            }
            return $event;
        }

        public function deleteWithCommunities(): bool
        {
            $this->communities()->detach();
            return $this->delete();
        }
}
