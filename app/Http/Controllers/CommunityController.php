<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    /**
     * Join a community
     */
    public function join(Community $community)
    {
        $user = Auth::user();
        
        // Authorize the action
        $this->authorize('join', $community);
        
        // Check if user is already a member
        if (!$community->communityMembers()->where('user_id', $user->id)->exists()) {
            // Increment the members count
            $community->increment('members');
            // Add user to community members
            $community->communityMembers()->attach($user->id, ['role' => 'member']);
        }

        return response()->json([
            'message' => 'Successfully joined the community',
            'member_count' => $community->members
        ], 200);
    }

    /**
     * Leave a community
     */
    public function leave(Community $community)
    {
        $user = Auth::user();
        
        // Authorize the action
        $this->authorize('leave', $community);

        // Check if user is a member
        if ($community->communityMembers()->where('user_id', $user->id)->exists()) {
            // Check if user is an admin
            $isAdmin = $community->admins()->where('users.id', $user->id)->exists();
            
            if ($isAdmin) {
                $adminCount = $community->admins()->count();
                if ($adminCount <= 1) {
                    return response()->json(['message' => 'Cannot leave as the last admin'], 422);
                }
            }

            // Decrement members count and remove user
            $community->decrement('members');
            $community->communityMembers()->detach($user->id);
        }

        return response()->json([
            'message' => 'Successfully left the community',
            'member_count' => $community->members
        ], 200);
    }
}
