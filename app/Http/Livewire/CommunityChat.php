<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Community;
use App\Models\CommunityMessage;
use Illuminate\Support\Facades\Auth;

class CommunityChat extends Component
{
    public $community;
    public $messages = [];
    public $newMessage = '';
    public $showChat = true;

    protected $rules = [
        'newMessage' => 'required|string|max:1000',
    ];

    protected $listeners = [
        'echo:community.{community.id},MessageSent' => 'handleNewMessage',
        'refresh' => 'loadMessages'
    ];

    public function mount(Community $community)
    {
        $this->community = $community;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = CommunityMessage::where('community_id', $this->community->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->take(100)
            ->get()
            ->toArray();
    }

    public function sendMessage()
    {
        $this->validate();

        if (!$this->isMember()) {
            session()->flash('error', 'You must be a member to send messages.');
            return;
        }

        CommunityMessage::create([
            'community_id' => $this->community->id,
            'user_id' => Auth::id(),
            'message' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->loadMessages();

        // Emit event to all listeners
        $this->emit('messageSent');
        
        // Scroll to bottom
        $this->dispatchBrowserEvent('scroll-to-bottom');
    }

    public function handleNewMessage($event)
    {
        $this->loadMessages();
        $this->dispatchBrowserEvent('scroll-to-bottom');
    }

    public function isMember()
    {
        if (!Auth::check()) {
            return false;
        }

        return $this->community->communityMembers()->where('user_id', Auth::id())->exists();
    }

    public function toggleChat()
    {
        $this->showChat = !$this->showChat;
    }

    public function render()
    {
        return view('livewire.community-chat');
    }
}
