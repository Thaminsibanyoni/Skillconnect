<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use Illuminate\Support\Str; // Import Str facade for slug generation
use Livewire\Component;
use Livewire\WithPagination;

class PageManagement extends Component
{
    use WithPagination;

    public $title = '';
    public $slug = '';
    public $content = '';
    public $status = 'draft'; // Default status
    public $pageId = null;
    public $showModal = false;

    // Validation rules
    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $this->pageId,
            'content' => 'required|string',
            'status' => 'required|in:published,draft',
        ];
    }

    // Automatically generate slug when title is updated
    public function updatedTitle($value)
    {
        // Only generate slug automatically if creating a new page or slug is empty
        if (!$this->pageId || empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function render()
    {
        $pages = Page::orderBy('title')->paginate(10);
        return view('livewire.admin.page-management', [
            'pages' => $pages,
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        $this->pageId = $id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = $page->content;
        $this->status = $page->status;
        $this->showModal = true;
    }

    public function save()
    {
        // Ensure slug is generated if empty before validation
        if (empty($this->slug) && !empty($this->title)) {
             $this->slug = Str::slug($this->title);
        }

        $this->validate();

        Page::updateOrCreate(['id' => $this->pageId], [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'status' => $this->status,
        ]);

        session()->flash('message', $this->pageId ? 'Page Updated Successfully.' : 'Page Created Successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Page::find($id)->delete();
        session()->flash('message', 'Page Deleted Successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->title = '';
        $this->slug = '';
        $this->content = '';
        $this->status = 'draft';
        $this->pageId = null;
        $this->resetErrorBag();
    }
}
