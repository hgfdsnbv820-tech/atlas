<?php

namespace App\Http\Livewire\Communities;

use Livewire\Component;
use App\Models\Community;

class ShowCommunity extends Component
{
    public $community;
    public $showMembersModal = false;

    protected $listeners = ['refresh' => '$refresh'];

    public function mount(Community $community)
    {
        $this->community = $community->load(['events' => function($query) {
            $query->orderBy('start_date', 'desc');
        }, 'communityMembers', 'messages']);
    }

    public function toggleMembersModal()
    {
        $this->showMembersModal = !$this->showMembersModal;
    }

    public function render()
    {
        return view('livewire.communities.show-community', [
            'members' => $this->community->communityMembers,
            'events' => $this->community->events
        ])->layout('layouts.base');
    }
}
