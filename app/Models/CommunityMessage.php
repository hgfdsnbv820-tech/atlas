<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Events\MessageSent;

class CommunityMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_id',
        'user_id',
        'message',
    ];

    protected $with = ['user'];

    protected static function booted()
    {
        static::created(function ($message) {
            broadcast(new MessageSent($message))->toOthers();
        });
    }

    /**
     * Get the community that owns the message.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the user that sent the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
