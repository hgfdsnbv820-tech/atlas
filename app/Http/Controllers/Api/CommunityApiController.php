<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Notifications\CommunityJoined;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class CommunityApiController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Community::class);

        $query = Community::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $communities = $query->orderBy('name')->paginate(15);

        return response()->json($communities);
    }

    public function show(Community $community)
    {
        $this->authorize('view', $community);
        return response()->json($community->loadCount('communityMembers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Community::class);

        $data = $request->validate([
            'name' => 'required|string|max:100|unique:communities,name',
            'slug' => 'nullable|string|max:120|unique:communities,slug',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['created_by'] = Auth::id();

        $community = Community::create($data);

        // Add creator as admin member
        $community->communityMembers()->attach(Auth::id(), ['role' => 'admin']);

        return response()->json($community, 201);
    }

    public function update(Request $request, Community $community)
    {
        $this->authorize('update', $community);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:100|unique:communities,name,' . $community->id,
            'slug' => 'sometimes|nullable|string|max:120|unique:communities,slug,' . $community->id,
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $community->update($data);

        return response()->json($community);
    }

    public function destroy(Community $community)
    {
        $this->authorize('delete', $community);
        $community->delete();
        return response()->json(['message' => 'Community deleted']);
    }

    public function join(Community $community)
    {
        $this->authorize('join', $community);
        $user = Auth::user();

        // Check if user is already a member
        $isMember = $community->communityMembers()->where('users.id', $user->id)->exists();
        
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

    public function leave(Community $community)
    {
        $this->authorize('leave', $community);
        $user = Auth::user();

        // Check if user is a member
        if ($community->communityMembers()->where('users.id', $user->id)->exists()) {
            // Check if user is an admin
            $isAdmin = $community->admins()->where('users.id', $user->id)->exists();
            
            if ($isAdmin) {
                $adminCount = $community->admins()->count();
                if ($adminCount <= 1) {
                    return response()->json([
                        'message' => 'Cannot leave as the last admin'
                    ], 422);
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
