<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use App\Traits\LogsAdminActivity;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class CouponManager extends Component
{
    use WithPagination, LogsAdminActivity;

    // Form properties
    public $code = '';
    public $type = 'percent';
    public $value = 0;
    public $min_order_amount = null;
    public $usage_limit = null;
    public $usage_limit_per_user = null;
    public $expires_at = null;
    public $is_active = true;

    public $couponId = null;
    public $showModal = false;

    protected function rules()
    {
        return [
            'code' => 'required|string|max:50|unique:coupons,code,' . $this->couponId,
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0' . ($this->type === 'percent' ? '|max:100' : ''),
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:0',
            'expires_at' => 'nullable|date',
            'is_active' => 'required|boolean',
        ];
    }

    public function generateCode()
    {
        $this->code = strtoupper(Str::random(10));
    }

    public function render()
    {
        $coupons = Coupon::latest()->paginate(15);
        return view('livewire.admin.coupon-manager', [
            'coupons' => $coupons,
        ])->layout('layouts.admin');
    }

     public function create()
    {
        $this->resetInputFields();
        $this->generateCode(); // Generate initial code
        $this->showModal = true;
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        $this->couponId = $id;
        $this->code = $coupon->code;
        $this->type = $coupon->type;
        $this->value = $coupon->value;
        $this->min_order_amount = $coupon->min_order_amount;
        $this->usage_limit = $coupon->usage_limit;
        $this->usage_limit_per_user = $coupon->usage_limit_per_user;
        $this->expires_at = $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : null; // Format for datetime-local input
        $this->is_active = $coupon->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $validatedData = $this->validate();
        // Ensure null values for empty optional fields
        $validatedData['min_order_amount'] = $validatedData['min_order_amount'] ?: null;
        $validatedData['usage_limit'] = $validatedData['usage_limit'] ?: null;
        $validatedData['usage_limit_per_user'] = $validatedData['usage_limit_per_user'] ?: null;
        $validatedData['expires_at'] = $validatedData['expires_at'] ?: null;


        $coupon = Coupon::updateOrCreate(['id' => $this->couponId], $validatedData);

        $action = $this->couponId ? 'updated_coupon' : 'created_coupon';
        $this->logAdminActivity($action, $coupon);

        session()->flash('message', $this->couponId ? 'Coupon Updated Successfully.' : 'Coupon Created Successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        $coupon = Coupon::find($id);
         if ($coupon) {
            $this->logAdminActivity('deleted_coupon', $coupon);
            $coupon->delete();
            session()->flash('message', 'Coupon Deleted Successfully.');
        } else {
             session()->flash('error', 'Coupon not found.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->resetExcept('showModal'); // Keep modal state if needed
        $this->reset(['code', 'type', 'value', 'min_order_amount', 'usage_limit', 'usage_limit_per_user', 'expires_at', 'is_active', 'couponId']);
        $this->type = 'percent'; // Reset default
        $this->is_active = true; // Reset default
        $this->resetErrorBag();
    }
}
