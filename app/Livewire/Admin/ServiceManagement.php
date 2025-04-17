<?php

namespace App\Livewire\Admin;

use App\Models\Service;
use App\Models\ServiceCategory;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceManagement extends Component
{
    use WithPagination;

    public $name = '';
    public $description = '';
    public $service_category_id = ''; // Use empty string for default select option
    public $serviceId = null;
    public $showModal = false;
    public $allCategories = []; // To hold categories for dropdown

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'service_category_id' => 'required|exists:service_categories,id', // Ensure category exists
    ];

    public function mount()
    {
        // Load categories once when component mounts
        $this->allCategories = ServiceCategory::orderBy('name')->get();
    }

    public function render()
    {
        $services = Service::with('serviceCategory') // Eager load category
                           ->orderBy('name')
                           ->paginate(10);

        return view('livewire.admin.service-management', [
            'services' => $services,
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $this->serviceId = $id;
        $this->name = $service->name;
        $this->description = $service->description;
        $this->service_category_id = $service->service_category_id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Service::updateOrCreate(['id' => $this->serviceId], [
            'name' => $this->name,
            'description' => $this->description,
            'service_category_id' => $this->service_category_id,
        ]);

        session()->flash('message', $this->serviceId ? 'Service Updated Successfully.' : 'Service Created Successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        // Consider adding checks if service is linked to orders before deleting
        Service::find($id)->delete();
        session()->flash('message', 'Service Deleted Successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->service_category_id = '';
        $this->serviceId = null;
        $this->resetErrorBag();
    }
}
