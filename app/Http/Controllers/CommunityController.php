<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Notifications\CommunityJoined;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    /**
     * Join or leave a community (toggle membership)
     */
    public function join(Community $community)
    {
        $user = Auth::user();
        
        // Authorize the action
        $this->authorize('join', $community);
        
        // Check if user is already a member
        $isMember = $community->communityMembers()->where('user_id', $user->id)->exists();
        
        if ($isMember) {
            // User is already a member, so leave the community
            
            // Check if user is an admin
            $isAdmin = $community->admins()->where('users.id', $user->id)->exists();
            
            if ($isAdmin) {
                $adminCount = $community->admins()->count();
                if ($adminCount <= 1) {
                    return response()->json([
                        'message' => 'Cannot leave as the last admin',
                        'is_member' => true
                    ], 422);
                }
            }

            // Decrement members count and remove user
            $community->decrement('members');
            $community->communityMembers()->detach($user->id);
            
            return response()->json([
                'message' => 'Successfully left the community',
                'member_count' => $community->members,
                'is_member' => false
            ], 200);
        } else {
            // User is not a member, so join the community
            
            // Increment the members count
            $community->increment('members');
            // Add user to community members
            $community->communityMembers()->attach($user->id, ['role' => 'member']);
            
            // Send email notification
            $user->notify(new CommunityJoined($community));
            
            return response()->json([
                'message' => 'Successfully joined the community',
                'member_count' => $community->members,
                'is_member' => true
            ], 200);
        }
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
