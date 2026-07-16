<?php

namespace App\Livewire\Payment;

use App\Models\Membership;
use App\Models\Payment;
use App\Notifications\PaymentReceivedNotification;
use App\Services\DocumentProcessingService;
use App\Services\PaymentService;
use App\Services\Payments\PaymentGatewayFactory;
use Livewire\Component;
use Livewire\WithFileUploads;

class InitiatePayment extends Component
{
    use WithFileUploads;

public $amount;
    public $provider;
    public $purpose;
    public $reference;
    public $paymentInstructions;
    public $showInstructions = false;
    public $membershipId;
    public $memberships;
    public $proofFile;
    public $useApiPayment = false;
    public $paymentInitiated = false;
    public $stkResponse = null;

protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'provider' => 'required|in:mpesa,ecocash',
        'purpose' => 'required|string|max:255',
        'membershipId' => 'required|exists:memberships,id',
        'proofFile' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
    ];

public array $purposeOptions = [
        'Annual Membership Fee',
        'Membership Renewal',
        'Penalty Payment',
        'Other Membership Payment',
    ];

public function mount()
    {
        $this->memberships = Membership::query()
            ->where('user_id', auth()->id())
            ->whereIn('status', [Membership::STATUS_APPROVED, Membership::STATUS_SUSPENDED, Membership::STATUS_EXPIRED])
            ->with('category')
            ->latest()
            ->get();

if ($this->memberships->count() > 0) {
            $membership = $this->memberships->first();

$this->membershipId = $membership->id;
            $this->amount = $membership->category?->annual_fee;
            $this->purpose = 'Annual Membership Fee';
        }
    }

public function generateReference()
    {
        $this->reference = PaymentService::generateReference();
    }

public function generateInstructions()
    {
        $this->validate(['amount' => 'required|numeric|min:0.01', 'provider' => 'required|in:mpesa,ecocash']);
        $this->reference = PaymentService::generateReference();
        $this->paymentInstructions = PaymentService::getPaymentInstructions($this->provider, $this->amount, $this->reference);
        $this->showInstructions = true;
    }

    /**
     * Initiate API-driven mobile payment
     */
    public function initiateApiPayment()
    {
        $this->validate();
        
        // Verify membership ownership
        $membership = Membership::where('id', $this->membershipId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$membership) {
            $this->addError('membershipId', 'Invalid membership selected.');
            return;
        }

        $this->reference = PaymentService::generateReference();
        
        // Create pending payment record
        $payment = Payment::create([
            'membership_id' => $this->membershipId,
            'amount' => $this->amount,
            'provider' => $this->provider,
            'purpose' => $this->purpose,
            'transaction_reference' => $this->reference,
            'proof_file' => null,
            'status' => 'pending',
        ]);

        // Initiate payment with gateway API
        try {
            $gateway = PaymentGatewayFactory::make($this->provider);
            
            if ($this->provider === 'mpesa') {
                $response = $gateway->initiateStkPush($payment);
            } else {
                $response = $gateway->initiatePayment($payment);
            }

            if ($response && isset($response['ResponseCode']) && $response['ResponseCode'] === '0') {
                $this->paymentInitiated = true;
                $this->stkResponse = $response;
                $this->dispatchBrowserEvent('notify', [
                    'type' => 'success',
                    'message' => 'Payment initiated! Check your phone to complete the transaction.'
                ]);
            } else {
                $payment->delete();
                $this->addError('provider', 'Failed to initiate payment. Please try again or upload proof manually.');
            }
        } catch (\Exception $e) {
            $payment->delete();
            $this->addError('provider', 'Payment service unavailable. Please upload proof manually.');
        }
    }

public function updatedMembershipId($value): void
    {
        $membership = $this->memberships->firstWhere('id', (int) $value);

        if ($membership && $membership->user_id === auth()->id()) {
            $this->amount = $membership->category?->annual_fee;
        } else {
            $this->membershipId = null;
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Invalid membership selected.'
            ]);
        }
    }

    public function submit()
    {
        $this->validate();

        // SECURITY: Verify membership ownership before creating payment
        $membership = Membership::where('id', $this->membershipId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$membership) {
            $this->addError('membershipId', 'You do not have permission to make a payment for this membership.');
            return;
        }

        // VALIDATION: Verify filename length to prevent path traversal
        if ($this->proofFile) {
            $originalName = $this->proofFile->getClientOriginalName();
            if (strlen($originalName) > 255) {
                $this->addError('proofFile', 'Filename is too long (max 255 characters).');
                return;
            }
        }

        if (!$this->reference) {
            $this->generateInstructions();
        }

        // Only store proof file if manually uploaded
        $proofPath = null;
        if ($this->proofFile) {
            $proofPath = DocumentProcessingService::processDocument($this->proofFile, 'payment-proofs');
        }

        $payment = Payment::create([
            'membership_id' => $this->membershipId,
            'amount' => $this->amount,
            'provider' => $this->provider,
            'purpose' => $this->purpose,
            'transaction_reference' => $this->reference,
            'proof_file' => $proofPath,
            'status' => 'pending',
        ]);

        auth()->user()->notify(new PaymentReceivedNotification($payment));

        return redirect()->route('member.dashboard')->with('success', 'Payment submitted successfully. It will be reviewed by the finance team.');
    }

    public function render()
    {
        return view('livewire.payment.initiate-payment')
            ->extends('layouts.app')
            ->section('content');
    }
}