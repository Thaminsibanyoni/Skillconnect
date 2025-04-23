<?php

namespace App\Livewire\Admin;

use App\Models\ProviderDocument;
use App\Models\User;
use App\Traits\LogsAdminActivity;
use Livewire\Component;
use Livewire\WithPagination;

class ProviderVerification extends Component
{
    use WithPagination, LogsAdminActivity;

    public $filterStatus = 'pending'; // Filter providers by overall status
    public $selectedProvider = null;
    public $rejectReason = '';
    public $rejectingDocumentId = null;
    public $showRejectModal = false;

    public function updatingFilterStatus() { $this->resetPage(); }

    public function viewDocuments(int $providerId)
    {
        $this->selectedProvider = User::with(['providerDocuments' => fn($q) => $q->orderBy('document_type')])
                                      ->where('id', $providerId)
                                      ->where('role', 'provider')
                                      ->first();
        $this->reset(['rejectReason', 'rejectingDocumentId', 'showRejectModal']); // Reset modal state
    }

    public function clearSelectedProvider()
    {
        $this->selectedProvider = null;
    }

    public function approveDocument(int $documentId)
    {
        $document = ProviderDocument::find($documentId);
        if ($document && $document->status !== 'approved') {
            $document->status = 'approved';
            $document->verified_at = now();
            $document->admin_notes = null; // Clear rejection notes
            $document->save();
            $this->logAdminActivity('approved_provider_document', $document->user, ['document_id' => $document->id, 'type' => $document->document_type]);
            session()->flash('doc_message', 'Document approved.');
            // Refresh selected provider data
            $this->selectedProvider?->load('providerDocuments');
            $this->checkAndApproveProvider($document->user); // Check if provider can be approved
        }
    }

    public function showRejectDocumentModal(int $documentId)
    {
        $this->rejectingDocumentId = $documentId;
        $this->rejectReason = '';
        $this->resetErrorBag();
        $this->showRejectModal = true;
    }

     public function rejectDocument()
    {
        $this->validate(['rejectReason' => 'required|string|max:255']);
        $document = ProviderDocument::find($this->rejectingDocumentId);

        if ($document && $document->status !== 'rejected') {
            $document->status = 'rejected';
            $document->verified_at = null;
            $document->admin_notes = $this->rejectReason;
            $document->save();
            $this->logAdminActivity('rejected_provider_document', $document->user, ['document_id' => $document->id, 'type' => $document->document_type, 'reason' => $this->rejectReason]);
            session()->flash('doc_message', 'Document rejected.');
            $this->closeRejectModal();
            $this->selectedProvider?->load('providerDocuments'); // Refresh
        } else {
             session()->flash('doc_error', 'Document not found or already rejected.');
             $this->closeRejectModal();
        }
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectingDocumentId = null;
        $this->rejectReason = '';
    }

    /**
     * Check if all required documents are approved and approve the provider.
     */
    protected function checkAndApproveProvider(User $provider)
    {
        // Define required document types
        $requiredDocs = ['id_document', 'ck_document', 'facial_image']; // Example
        $approvedDocs = $provider->providerDocuments()
                                ->where('status', 'approved')
                                ->whereIn('document_type', $requiredDocs)
                                ->pluck('document_type')
                                ->toArray();

        if (count(array_intersect($requiredDocs, $approvedDocs)) === count($requiredDocs)) {
            // All required docs are approved, approve the provider if they are pending
            if ($provider->status === 'pending') {
                $provider->status = 'approved';
                $provider->save();
                $this->logAdminActivity('approved_provider_via_docs', $provider);
                session()->flash('provider_message', 'All required documents approved. Provider approved.');
                // Refresh the main list by potentially changing filter or re-rendering
                $this->dispatch('$refresh'); // Simple refresh
            }
        }
    }


    public function render()
    {
        $query = User::query()
            ->where('role', 'provider')
            ->withCount(['providerDocuments as pending_documents_count' => fn($q) => $q->where('status', 'pending')])
            ->withCount(['providerDocuments as approved_documents_count' => fn($q) => $q->where('status', 'approved')])
            ->withCount(['providerDocuments as rejected_documents_count' => fn($q) => $q->where('status', 'rejected')]);


        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Prioritize providers with pending documents if filtering by pending status
        if ($this->filterStatus === 'pending') {
            $query->orderBy('pending_documents_count', 'desc');
        }

        $providers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.admin.provider-verification', [
            'providers' => $providers
        ])->layout('layouts.admin');
    }
}
