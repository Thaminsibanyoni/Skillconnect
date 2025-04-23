<?php

namespace App\Livewire\Admin;

use App\Models\Dispute;
use App\Traits\LogsAdminActivity;
use Livewire\Component;
use Livewire\WithPagination;

class DisputeManagement extends Component
{
    use WithPagination, LogsAdminActivity;

    public $statusFilter = 'open'; // Default to open disputes

    public function updatingStatusFilter() { $this->resetPage(); }

    // TODO: Add methods for viewing details, updating status, adding resolution notes

    public function render()
    {
        $query = Dispute::with(['order', 'reporter:id,name', 'reportedUser:id,name', 'resolvedByAdmin:id,name']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $disputes = $query->latest()->paginate(15);

        return view('livewire.admin.dispute-management', [
            'disputes' => $disputes
        ])->layout('layouts.admin');
    }
}
