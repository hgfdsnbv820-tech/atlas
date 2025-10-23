<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Community as CommunityModel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class Community extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $confirmingDeletion = false;
    public $deletingId = null;

    // Mark a community for deletion and open the confirmation modal
    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->confirmingDeletion = true;
    }

    // Cancel the delete flow and close the modal
    public function cancelDelete()
    {
        $this->deletingId = null;
        $this->confirmingDeletion = false;
    }

    // Perform deletion after user confirms
    public function deleteConfirmed()
    {
        if (!$this->deletingId) {
            session()->flash('error', 'No community selected for deletion.');
            return;
        }

        $community = CommunityModel::find($this->deletingId);

        if (!$community) {
            session()->flash('error', 'Community not found.');
            $this->cancelDelete();
            return;
        }

        // Authorization check
        if (Gate::denies('delete', $community)) {
            session()->flash('error', 'You are not authorized to delete this community.');
            $this->cancelDelete();
            return;
        }

        try {
            $community->delete();
            session()->flash('success', 'Community deleted successfully.');
            $this->emit('communityDeleted', $this->deletingId);
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while deleting the community.');
        }

        $this->cancelDelete();
    }

    public function render()
    {
        return view('livewire.community', [
            'communities' => CommunityModel::withCount('communityMembers')->with('events')->orderBy('name')->paginate(10),
        ]);
    }
}
