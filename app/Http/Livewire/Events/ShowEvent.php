<?php

namespace App\Http\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class ShowEvent extends Component
{
    public $event;
    public $organizer;

    public function mount($event)
    {
        $event = Event::findOrFail($event);

        // Authorization: allow guests to view public events via policy
        if (Gate::denies('view', $event)) {
            abort(403);
        }

        $this->event = $event;
        $this->organizer = $event->organizer_id ? User::find($event->organizer_id) : null;
    }

    public function render()
    {
        return view('livewire.events.show-event', [
            'event' => $this->event,
            'organizer' => $this->organizer,
        ])->layout('layouts.base');
    }
}
