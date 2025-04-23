<?php

namespace App\Livewire\Admin;

use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class SubscriptionPlanManagement extends Component
{
    use WithPagination;

    // Form Properties
    public $planId = null;
    public $name = '';
    public $slug = '';
    public $description = '';
    public $price = 0.00;
    public $currency = 'ZAR';
    public $interval = 'month';
    public $interval_count = 1;
    public $max_cities = null;
    public $features = []; // Store as array, manage input as comma-separated string
    public $features_input = ''; // Input field for features
    public $is_active = true;

    public $showModal = false;

    protected function rules() {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug,' . $this->planId,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'interval' => 'required|in:day,week,month,year',
            'interval_count' => 'required|integer|min:1',
            'max_cities' => 'nullable|integer|min:0',
            'features_input' => 'nullable|string', // Validate the input string
            'is_active' => 'required|boolean',
        ];
    }

    // Auto-generate slug from name
    public function updatedName($value)
    {
        // Only generate slug automatically if creating a new plan or slug is empty/matches old slug
        if (!$this->planId || empty($this->slug) || $this->slug === Str::slug($this->name)) {
             $this->slug = Str::slug($value);
        }
    }

    // Convert features array to string for editing
    private function featuresArrayToString(array $features = null): string
    {
        return $features ? implode(', ', $features) : '';
    }

    // Convert input string to features array for saving
    private function featuresStringToArray(string $featuresInput = null): array
    {
        if (empty($featuresInput)) {
            return [];
        }
        // Trim whitespace from each feature after splitting by comma
        return array_map('trim', explode(',', $featuresInput));
    }

     public function createPlan()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function editPlan(SubscriptionPlan $plan)
    {
        $this->planId = $plan->id;
        $this->name = $plan->name;
        $this->slug = $plan->slug;
        $this->description = $plan->description;
        $this->price = $plan->price;
        $this->currency = $plan->currency;
        $this->interval = $plan->interval;
        $this->interval_count = $plan->interval_count;
        $this->max_cities = $plan->max_cities;
        $this->features = $plan->features ?? [];
        $this->features_input = $this->featuresArrayToString($this->features);
        $this->is_active = $plan->is_active;
        $this->showModal = true;
    }

     public function savePlan()
    {
         // Ensure slug is generated if empty before validation
        if (empty($this->slug) && !empty($this->name)) {
             $this->slug = Str::slug($this->name);
        }

        $validatedData = $this->validate();

        // Convert features input string back to array
        $featuresArray = $this->featuresStringToArray($this->features_input);

        SubscriptionPlan::updateOrCreate(['id' => $this->planId], [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'interval' => $this->interval,
            'interval_count' => $this->interval_count,
            'max_cities' => $this->max_cities,
            'features' => $featuresArray,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', $this->planId ? 'Subscription Plan Updated.' : 'Subscription Plan Created.');
        $this->closeModal();
    }

     public function deletePlan(SubscriptionPlan $plan)
    {
         // TODO: Add check if plan is currently assigned to any users before deleting
        $plan->delete();
        session()->flash('message', 'Subscription Plan Deleted.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

     private function resetInputFields()
    {
        $this->planId = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->price = 0.00;
        $this->currency = 'ZAR';
        $this->interval = 'month';
        $this->interval_count = 1;
        $this->max_cities = null;
        $this->features = [];
        $this->features_input = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $plans = SubscriptionPlan::orderBy('price')->paginate(10);
        return view('livewire.admin.subscription-plan-management', [
            'plans' => $plans,
        ])->layout('layouts.admin');
    }
}
