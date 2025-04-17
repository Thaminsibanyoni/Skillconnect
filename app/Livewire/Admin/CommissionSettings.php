<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Config; // Import Config facade

class CommissionSettings extends Component
{
    public $commissionType;
    public $commissionRate;

    protected $rules = [
        'commissionType' => 'required|in:percentage,fixed',
        'commissionRate' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        // Load current settings from config
        $this->commissionType = config('skillconnect.commission.type', 'percentage');
        $this->commissionRate = config('skillconnect.commission.rate', 15.00);
    }

    public function saveSettings()
    {
        $this->validate();

        // **Important:** In a real application, update these values in a database
        // or use a dedicated settings package. Modifying .env or config files
        // directly at runtime is generally not recommended.
        // For this example, we'll just flash a success message.

        // Example of how you *might* update .env (requires a package like 'laravel-dotenv-editor'):
        // try {
        //     DotenvEditor::setKey('COMMISSION_TYPE', $this->commissionType);
        //     DotenvEditor::setKey('COMMISSION_RATE', $this->commissionRate);
        //     DotenvEditor::save();
        //     // Clear config cache if needed: Artisan::call('config:cache');
        //     session()->flash('message', 'Commission settings updated successfully. Config cache may need clearing.');
        // } catch (\Exception $e) {
        //     session()->flash('error', 'Failed to update settings: ' . $e->getMessage());
        // }

        // Simulate saving for now
        session()->flash('message', 'Commission settings updated successfully (simulation).');

        // Optionally reload the values from config to reflect changes if they were actually saved
        // $this->mount();
    }


    public function render()
    {
        return view('livewire.admin.commission-settings')
               ->layout('layouts.admin');
    }
}
