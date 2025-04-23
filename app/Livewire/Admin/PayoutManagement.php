<?php

namespace App\Livewire\Admin;

use App\Models\PayoutRequest;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class PayoutManagement extends Component
{
    use WithPagination;

    public $statusFilter = 'pending'; // Default to pending requests
    public $rejectReason = '';
    public $rejectingRequestId = null;
    public $showRejectModal = false;

    // Reset pagination when filters change
    public function updatingStatusFilter() { $this->resetPage(); }

    public function approveRequest(int $requestId)
    {
        $request = PayoutRequest::where('id', $requestId)->where('status', 'pending')->first();
        if ($request) {
            $request->status = 'approved';
            $request->save();
            session()->flash('message', 'Payout request approved. Ready to be processed.');
        } else {
            session()->flash('error', 'Request not found or not pending.');
        }
    }

    public function showRejectModal(int $requestId)
    {
        $this->rejectingRequestId = $requestId;
        $this->rejectReason = '';
        $this->resetErrorBag();
        $this->showRejectModal = true;
    }

    public function rejectRequest()
    {
        $this->validate(['rejectReason' => 'required|string|max:255']);

        $request = PayoutRequest::find($this->rejectingRequestId);
        if ($request && $request->status === 'pending') {
            $request->status = 'rejected';
            $request->notes = $this->rejectReason;
            $request->save();
            session()->flash('message', 'Payout request rejected.');
            $this->closeRejectModal();
        } else {
            session()->flash('error', 'Request not found or cannot be rejected.');
            $this->closeRejectModal();
        }
    }

     public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectingRequestId = null;
        $this->rejectReason = '';
    }


    public function markAsProcessed(int $requestId)
    {
        $payoutRequest = PayoutRequest::with('user.wallet')
                            ->where('id', $requestId)
                            ->where('status', 'approved') // Only process approved requests
                            ->first();

        if (!$payoutRequest) {
            session()->flash('error', 'Payout request not found or not approved.');
            return;
        }

        $provider = $payoutRequest->user;
        $wallet = $provider->wallet;
        $amount = $payoutRequest->amount;

        if (!$wallet || $wallet->balance < $amount) {
             session()->flash('error', 'Provider has insufficient wallet balance.');
             return;
        }

        DB::beginTransaction();
        try {
            // 1. Deduct amount from wallet
            $wallet->decrement('balance', $amount);

            // 2. Create Payout Transaction
            $transaction = Transaction::create([
                'user_id' => $provider->id,
                'order_id' => null,
                'type' => 'payout',
                'amount' => -$amount, // Store as negative for withdrawal
                'status' => 'completed', // Payout itself is completed
                'payment_method' => 'manual', // Or gateway if using API payout
                'transaction_reference' => 'PAYOUT_' . $payoutRequest->id,
                'description' => 'Manual payout processed by admin.'
            ]);

            // 3. Update Payout Request Status
            $payoutRequest->status = 'processed';
            $payoutRequest->processed_at = now();
            $payoutRequest->transaction_id = $transaction->id; // Link to transaction
            $payoutRequest->save();

            DB::commit();
            session()->flash('message', 'Payout marked as processed successfully.');

            // TODO: Notify provider about processed payout

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing payout: ' . $e->getMessage(), ['request_id' => $requestId]);
            session()->flash('error', 'An error occurred while processing the payout.');
        }
    }


    public function render()
    {
        $query = PayoutRequest::with(['user:id,name,email', 'transaction:id,created_at']); // Eager load needed relations

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $requests = $query->latest()->paginate(10);

        return view('livewire.admin.payout-management', [
            'requests' => $requests
        ])->layout('layouts.admin');
    }
}
