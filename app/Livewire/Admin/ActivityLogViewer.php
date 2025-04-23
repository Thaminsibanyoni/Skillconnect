<?php

namespace App\Livewire\Admin;

use App\Models\AdminActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogViewer extends Component
{
    use WithPagination;

    public $searchAdmin = '';
    public $searchAction = '';

    public function updatingSearchAdmin() { $this->resetPage(); }
    public function updatingSearchAction() { $this->resetPage(); }


    public function render()
    {
        $query = AdminActivityLog::with(['adminUser:id,name', 'target']); // Eager load

        if ($this->searchAdmin) {
            $query->whereHas('adminUser', fn($q) => $q->where('name', 'like', '%'.$this->searchAdmin.'%'));
        }
        if ($this->searchAction) {
            $query->where('action', 'like', '%'.$this->searchAction.'%');
        }

        $logs = $query->latest()->paginate(20);

        return view('livewire.admin.activity-log-viewer', [
            'logs' => $logs
        ])->layout('layouts.admin');
    }
}
