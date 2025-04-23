<?php

namespace App\Livewire\Admin;

use App\Models\Faq;
use App\Traits\LogsAdminActivity;
use Livewire\Component;
use Livewire\WithPagination;

class FaqManager extends Component
{
    use WithPagination, LogsAdminActivity;

    public $category = '';
    public $question = '';
    public $answer = '';
    public $display_order = 0;
    public $is_published = true;
    public $faqId = null;
    public $showModal = false;

    protected $rules = [
        'category' => 'nullable|string|max:100',
        'question' => 'required|string|max:255',
        'answer' => 'required|string',
        'display_order' => 'required|integer|min:0',
        'is_published' => 'required|boolean',
    ];

    public function render()
    {
        $faqs = Faq::orderBy('display_order')->orderBy('category')->paginate(15);
        return view('livewire.admin.faq-manager', [
            'faqs' => $faqs,
        ])->layout('layouts.admin');
    }

     public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        $this->faqId = $id;
        $this->category = $faq->category;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->display_order = $faq->display_order;
        $this->is_published = $faq->is_published;
        $this->showModal = true;
    }

    public function save()
    {
        $validatedData = $this->validate();

        $faq = Faq::updateOrCreate(['id' => $this->faqId], $validatedData);

        $action = $this->faqId ? 'updated_faq' : 'created_faq';
        $this->logAdminActivity($action, $faq);

        session()->flash('message', $this->faqId ? 'FAQ Updated Successfully.' : 'FAQ Created Successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        $faq = Faq::find($id);
        if ($faq) {
            $this->logAdminActivity('deleted_faq', $faq);
            $faq->delete();
            session()->flash('message', 'FAQ Deleted Successfully.');
        } else {
             session()->flash('error', 'FAQ not found.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset(['category', 'question', 'answer', 'display_order', 'is_published', 'faqId']);
        $this->is_published = true; // Reset boolean default
        $this->display_order = 0; // Reset integer default
        $this->resetErrorBag();
    }
}
