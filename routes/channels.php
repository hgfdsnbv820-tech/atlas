<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Community;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Community chat channel - public for now, can be restricted to members
Broadcast::channel('community.{communityId}', function ($user, $communityId) {
    // Allow all authenticated users to listen
    // To restrict to members only, uncomment the line below:
    // return Community::find($communityId)->communityMembers->contains($user->id);
    return true;
});
