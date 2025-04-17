<?php

namespace App\Livewire\Admin;

use App\Models\ServiceCategory;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceCategoryManagement extends Component
{
    use WithPagination;

    public $name = '';
    public $description = '';
    public $icon = '';
    public $categoryId = null;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255|unique:service_categories,name',
        'description' => 'nullable|string',
        'icon' => 'nullable|string|max:255',
    ];

    // Custom rule for updates to ignore the current category ID
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:service_categories,name,' . $this->categoryId,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ];
    }

    public function render()
    {
        $categories = ServiceCategory::orderBy('name')->paginate(10);
        return view('livewire.admin.service-category-management', [
            'categories' => $categories,
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $category = ServiceCategory::findOrFail($id);
        $this->categoryId = $id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->icon = $category->icon;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        ServiceCategory::updateOrCreate(['id' => $this->categoryId], [
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
        ]);

        session()->flash('message', $this->categoryId ? 'Category Updated Successfully.' : 'Category Created Successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        // Consider adding checks if category is in use before deleting
        ServiceCategory::find($id)->delete();
        session()->flash('message', 'Category Deleted Successfully.');
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
        $this->icon = '';
        $this->categoryId = null;
        $this->resetErrorBag(); // Clear validation errors
    }
}
