<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Community;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CreateCommunity extends Component
{
    use WithFileUploads;

    public $name;
    public $slug;
    public $description;
    public $is_public = true;
    public $coverImage;
    public $associatedEvents = [];
    public $availableEvents = [];

    protected $rules = [
        'name' => 'required|string|max:100|unique:communities,name',
        'slug' => 'nullable|string|max:120|unique:communities,slug',
        'description' => 'nullable|string',
        'coverImage' => 'nullable|image|max:2048',
        'is_public' => 'boolean',
    ];

    public function store()
    {
        $this->validate();

        // Authorization check
        $this->authorize('create', Community::class);

        // Auto-generate slug if not provided
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }

        $coverImagePath = null;

        // Handle image upload
        if ($this->coverImage) {
            $coverImagePath = $this->coverImage->store('communities', 'public');
        }

        $community = Community::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_public' => $this->is_public,
            'cover_image' => $coverImagePath,
            'created_by' => Auth::id(),
        ]);

        // Add creator as admin member
        $community->communityMembers()->attach(Auth::id(), ['role' => 'admin']);

    // load associated events (none at creation)
    $this->associatedEvents = [];

        session()->flash('success', 'Community created successfully!');

        return redirect()->route('community.index');
    }

    public function render()
    {
        return view('livewire.create-community');
    }
}
