<?php

namespace App\Livewire\Admin;

use App\Models\SupportTicket;
use App\Traits\LogsAdminActivity;
use Livewire\Component;
use Livewire\WithPagination;

class SupportTicketManagement extends Component
{
     use WithPagination, LogsAdminActivity;

    public $statusFilter = 'open'; // Default to open tickets
    public $priorityFilter = '';

    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingPriorityFilter() { $this->resetPage(); }

    // TODO: Add methods for viewing details, assigning admin, replying, resolving

    public function render()
    {
        $query = SupportTicket::with(['user:id,name', 'assignedAdmin:id,name']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        $tickets = $query->latest()->paginate(15);

        return view('livewire.admin.support-ticket-management', [
            'tickets' => $tickets
        ])->layout('layouts.admin');
    }
}
