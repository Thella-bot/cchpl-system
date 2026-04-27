<?php
namespace App\Livewire\Membership;

use App\Models\Membership;
use App\Models\MembershipCategory;
use App\Models\MembershipDocument;
use App\Notifications\ApplicationReceivedNotification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class ApplicationForm extends Component
{
    use WithFileUploads;

    #[Validate('required|exists:membership_categories,id')]
    public ?int $selected_category_id = null;

    #[Validate('required|file|mimes:pdf,doc,docx|max:5120')]
    public $cv_file = null;

    #[Validate('required|file|mimes:pdf,jpg,jpeg,png|max:5120')]
    public $certificates_file = null;

    #[Validate('required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120')]
    public $employment_letter_file = null;

    public function messages(): array
    {
        return [
            'selected_category_id.required' => 'Please select a membership category.',
            'selected_category_id.exists'   => 'Please select a valid membership category.',
            'cv_file.required'              => 'A CV or resume is required.',
            'cv_file.mimes'                 => 'CV must be a PDF or Word document (max 5MB).',
            'certificates_file.required'    => 'Certificate proof is required.',
            'certificates_file.mimes'       => 'Certificate proof must be a PDF, JPG or PNG (max 5MB).',
            'employment_letter_file.required' => 'An employment letter or student proof is required.',
            'employment_letter_file.mimes'    => 'Employment letter must be a PDF, Word document, JPG or PNG (max 5MB).',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        if (!auth()->check()) {
            session()->flash('error', '❌ You must be logged in to apply.');
            return;
        }

        // Prevent duplicate active or pending applications
        $existing = Membership::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existing) {
            session()->flash('error', '❌ You already have an active or pending membership application.');
            return;
        }

        // Create membership record.
        // expiry_date is intentionally NOT set here — it is only set when
        // payment is verified and the membership is activated (Bylaws 1.3).
        $membership = Membership::create([
            'user_id'     => auth()->id(),
            'category_id' => $this->selected_category_id,
            'status'      => 'pending',
        ]);

        // Store uploaded documents
        $documentTypes = [
            'cv_file'                => 'CV',
            'certificates_file'      => 'Certificate',
            'employment_letter_file' => 'Employment Letter',
        ];

        foreach ($documentTypes as $property => $type) {
            if ($this->$property) {
                $path = $this->$property->store('applications', 'public');
                MembershipDocument::create([
                    'membership_id' => $membership->id,
                    'document_type' => $type,
                    'file_path'     => $path,
                    'original_name' => $this->$property->getClientOriginalName(),
                    'status'        => 'pending',
                ]);
            }
        }

        // Notify user
        auth()->user()->notify(new ApplicationReceivedNotification($membership));

        session()->flash(
            'message',
            '✅ Application submitted successfully! The Membership Committee will review within 60 days.'
        );

        // FIX: redirect to the correct named route, not the non-existent /dashboard
        $this->redirect(route('member.dashboard'));
    }

    public function render()
    {
        return view('livewire.membership.application-form', [
            'categories' => MembershipCategory::query()
                ->where('name', '!=', 'Honorary')
                ->orderBy('annual_fee')
                ->get(),
        ])->extends('layouts.app')->section('content');
    }
}
