<?php

namespace App\Livewire\Payment;

use App\Models\Membership;
use App\Models\Payment;
use App\Notifications\PaymentReceivedNotification;
use App\Services\PaymentService;
use Livewire\Component;

class InitiatePayment extends Component
{
    public $amount;
    public $provider;
    public $purpose;
    public $reference;
    public $paymentInstructions;
    public $showInstructions = false;
    public $membershipId;
    public $memberships;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'provider' => 'required|in:mpesa,ecocash',
        'purpose' => 'required|string|max:255',
        'membershipId' => 'required|exists:memberships,id',
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

    public function updatedMembershipId($value): void
    {
        $membership = $this->memberships->firstWhere('id', (int) $value);

        if ($membership) {
            $this->amount = $membership->category?->annual_fee;
        }
    }

    public function submit()
    {
        $this->validate();

        if (!$this->reference) {
            $this->generateInstructions();
        }

        $payment = Payment::create([
            'membership_id' => $this->membershipId,
            'amount' => $this->amount,
            'provider' => $this->provider,
            'purpose' => $this->purpose,
            'transaction_reference' => $this->reference,
            'status' => 'pending',
        ]);

        auth()->user()->notify(new PaymentReceivedNotification($payment));

        return redirect()->route('member.dashboard')->with('success', 'Payment initiated successfully. Please follow the instructions to complete the payment.');
    }

    public function render()
    {
        return view('livewire.payment.initiate-payment')
            ->extends('layouts.app')
            ->section('content');
    }
}
