# CCHPL System - Developer Reference Guide

Technical reference and code examples for extending and maintaining the CCHPL System.

## Table of Contents
1. [Database Schema](#database-schema)
2. [Model Reference](#model-reference)
3. [Livewire Components](#livewire-components)
4. [Service Classes](#service-classes)
5. [Request/Response Examples](#request-response-examples)
6. [Common Tasks](#common-tasks)
7. [Extending the System](#extending-the-system)

---

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255),
    phone VARCHAR(20),
    organization VARCHAR(255),
    remember_token VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Membership Categories Table
```sql
CREATE TABLE membership_categories (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) UNIQUE,
    annual_fee DECIMAL(10, 2),
    voting_rights BOOLEAN,
    eligibility_criteria TEXT,
    other_notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Memberships Table
```sql
CREATE TABLE memberships (
    id BIGINT PRIMARY KEY,
    user_id BIGINT FOREIGN KEY (users.id),
    category_id BIGINT FOREIGN KEY (membership_categories.id),
    status ENUM('pending', 'approved', 'rejected', 'suspended', 'expired'),
    expiry_date DATE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Payments Table
```sql
CREATE TABLE payments (
    id BIGINT PRIMARY KEY,
    membership_id BIGINT FOREIGN KEY (memberships.id),
    amount DECIMAL(10, 2),
    provider ENUM('mpesa', 'ecocash'),
    transaction_reference VARCHAR(255) UNIQUE,
    proof_file VARCHAR(255),
    status ENUM('pending', 'verified', 'rejected'),
    verification_notes TEXT,
    verified_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Membership Documents Table
```sql
CREATE TABLE membership_documents (
    id BIGINT PRIMARY KEY,
    membership_id BIGINT FOREIGN KEY (memberships.id),
    document_type VARCHAR(255),
    file_path VARCHAR(255),
    original_name VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## Model Reference

### User Model

```php
use App\Models\User;

// Create user
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password'),
    'phone' => '+266 12345678',
    'organization' => 'CCHPL'
]);

// Get user's memberships
$memberships = $user->memberships; // Collection of Membership models
$activeMembership = $user->memberships()->where('status', 'approved')->first();

// Check if user has active membership
if ($user->memberships()->where('status', 'approved')->exists()) {
    // User is a member
}
```

### Membership Model

```php
use App\Models\Membership;

// Create membership
$membership = Membership::create([
    'user_id' => $user->id,
    'category_id' => $category->id,
    'status' => 'pending',
    'expiry_date' => now()->addYear()
]);

// Access relationships
echo $membership->user->name;           // Get user name
echo $membership->category->name;       // Get category
echo $membership->category->annual_fee; // Get fee

// Get membership documents
foreach ($membership->documents as $doc) {
    echo $doc->document_type;  // "CV", "Certificate", etc
    echo $doc->file_path;      // Path in storage
}

// Get payments
foreach ($membership->payments as $payment) {
    echo $payment->status;               // "pending", "verified", "rejected"
    echo $payment->transaction_reference; // "CCHPL-20240115-1234"
}

// Query examples
$pendingMemberships = Membership::where('status', 'pending')->get();
$expiredMemberships = Membership::where('expiry_date', '<', now())->get();
$professionalMembers = Membership::whereHas('category', function($q) {
    $q->where('name', 'Professional');
})->where('status', 'approved')->get();
```

### MembershipCategory Model

```php
use App\Models\MembershipCategory;

// Get all categories
$categories = MembershipCategory::all();

// Get category with members
$category = MembershipCategory::find(1);
$memberCount = $category->memberships()->where('status', 'approved')->count();
$totalFees = $category->memberships()
    ->where('status', 'approved')
    ->count() * $category->annual_fee;

// Find category by name
$professional = MembershipCategory::where('name', 'Professional')->first();
```

### Payment Model

```php
use App\Models\Payment;

// Create payment
$payment = Payment::create([
    'membership_id' => $membership->id,
    'amount' => 400,
    'provider' => 'mpesa',
    'transaction_reference' => 'CCHPL-20240115-1234',
    'status' => 'pending'
]);

// Verify payment
$payment->update([
    'status' => 'verified',
    'proof_file' => 'proofs/screenshot.jpg',
    'verified_at' => now(),
    'verification_notes' => 'Verified against M-Pesa records'
]);

// Access membership
echo $payment->membership->user->name; // Get payer name

// Query examples
$pendingPayments = Payment::where('status', 'pending')
    ->orderBy('created_at', 'asc')
    ->get();

$mpesaPayments = Payment::where('provider', 'mpesa')
    ->where('status', 'verified')
    ->sum('amount');
```

### MembershipDocument Model

```php
use App\Models\MembershipDocument;

// Create document record
$doc = MembershipDocument::create([
    'membership_id' => $membership->id,
    'document_type' => 'CV',
    'file_path' => 'applications/user_1_cv.pdf',
    'original_name' => 'John_CV.pdf'
]);

// Query documents by type
$certs = MembershipDocument::where('document_type', 'Certificate')->get();

// Delete document (and file if needed)
$doc->delete();
```

---

## Livewire Components

### ApplicationForm Component

```php
namespace App\Livewire\Membership;

class ApplicationForm extends Component {
    // Properties
    public $selected_category_id = '';
    public $cv_file = null;
    public $certificates_file = null;
    public $employment_letter_file = null;

    // Validation rules (Livewire v3 syntax)
    #[Validate('required')]
    public $field = '';

    // Methods
    public function updated($propertyName) {
        // Runs when any property changes
        $this->validateOnly($propertyName);
    }

    public function submit() {
        // Handle form submission
        $this->validate();
        // ... create membership, store files
    }

    public function render() {
        return view('livewire.membership.application-form');
    }
}
```

### InitiatePayment Component

```php
namespace App\Livewire\Payment;

class InitiatePayment extends Component {
    // Two-step workflow
    public $step = 1; // 1: payment details, 2: proof upload

    // Step 1 properties
    public $selected_membership_id = '';
    public $payment_amount = '';
    public $payment_provider = 'mpesa';

    // Step 2 properties
    public $reference_code = '';
    public $proof = null;
    public $instructions = '';

    public function initiatePayment() {
        // Generate reference and display instructions
    }

    public function submitProof() {
        // Upload proof and create payment record
    }

    public function render() {
        return view('livewire.payment.initiate-payment');
    }
}
```

---

## Service Classes

### PaymentService

```php
namespace App\Services;

use App\Models\Payment;

class PaymentService {
    /**
     * Verify a payment
     * @param Payment $payment
     * @param bool $approved
     * @return bool
     */
    public static function verifyPayment(Payment $payment, bool $approved = true): bool {
        if ($approved) {
            $payment->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verification_notes' => 'Payment verified by administrator'
            ]);
            $payment->membership->update(['status' => 'approved']);
            return true;
        } else {
            $payment->update([
                'status' => 'rejected',
                'verification_notes' => 'Payment proof rejected'
            ]);
            return false;
        }
    }

    /**
     * Generate reference code
     * @return string
     */
    public static function generateReference(): string {
        return 'CCHPL-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get payment instructions
     * @param string $provider
     * @param string $amount
     * @param string $reference
     * @return string
     */
    public static function getPaymentInstructions(string $provider, string $amount, string $reference): string {
        // Returns formatted instructions
    }
}
```

Usage:
```php
use App\Services\PaymentService;

// Generate reference
$ref = PaymentService::generateReference();

// Verify payment
$success = PaymentService::verifyPayment($payment, true);

// Get instructions
$instructions = PaymentService::getPaymentInstructions('mpesa', '400', $ref);
```

---

## Request/Response Examples

### Create Membership Application

**Request Data:**
```php
$data = [
    'selected_category_id' => 1,
    'cv_file' => UploadedFile $instance,
    'certificates_file' => UploadedFile $instance,
    'employment_letter_file' => UploadedFile $instance
];
```

**Response:**
```php
// Success
session()->flash('message', '✅ Application submitted successfully!');
redirect('/dashboard');

// Error
session()->flash('error', '❌ Error submitting application');
```

### Initiate Payment

**Request Data (Step 1):**
```php
$data = [
    'selected_membership_id' => 5,
    'payment_amount' => 400,
    'payment_provider' => 'mpesa'
];
```

**Response (Step 1):**
```php
// Payment record created
// Reference code generated: CCHPL-20240115-4567
// Instructions displayed to user
// UI updates to Step 2
```

**Request Data (Step 2):**
```php
$data = [
    'proof' => UploadedFile $instance // JPG/PNG image
];
```

**Response (Step 2):**
```php
// Proof stored in storage/app/public/proofs/
// Payment record updated with proof_file path
session()->flash('message', '✅ Payment proof uploaded successfully!');
redirect('/dashboard');
```

---

## Common Tasks

### Approve a Membership Application

```php
use App\Models\Membership;

$membership = Membership::findOrFail($id);
$membership->update(['status' => 'approved']);

// Send notification email
// $membership->user->notify(new ApplicationApproved($membership));
```

### Verify and Activate Payment

```php
use App\Models\Payment;
use App\Services\PaymentService;

$payment = Payment::findOrFail($id);
PaymentService::verifyPayment($payment, true);

// Membership now has status 'approved'
// Send confirmation email
// $payment->membership->user->notify(new PaymentVerified($payment));
```

### Get Dashboard Statistics

```php
// Total applications
$pendingCount = Membership::where('status', 'pending')->count();
$approvedCount = Membership::where('status', 'approved')->count();

// Revenue
$totalRevenue = Payment::where('status', 'verified')->sum('amount');

// By category
$categoryStats = MembershipCategory::with('memberships')
    ->get()
    ->map(fn($cat) => [
        'category' => $cat->name,
        'members' => $cat->memberships()->where('status', 'approved')->count()
    ]);

// Expiring soon
$expiringMemberships = Membership::where('expiry_date', '>=', now())
    ->where('expiry_date', '<=', now()->addDays(30))
    ->count();
```

### Export Member List

```php
$members = Membership::where('status', 'approved')
    ->with('user', 'category')
    ->get()
    ->map(fn($m) => [
        'name' => $m->user->name,
        'email' => $m->user->email,
        'category' => $m->category->name,
        'expiry' => $m->expiry_date,
        'phone' => $m->user->phone
    ]);

// Convert to CSV or Excel
```

### Send Renewal Reminder

```php
$expiringMemberships = Membership::where('expiry_date', '<=', now()->addDays(14))
    ->where('expiry_date', '>=', now())
    ->get();

foreach ($expiringMemberships as $membership) {
    // $membership->user->notify(new MembershipExpiringReminder($membership));
}
```

---

## Extending the System

### Add Payment Gateway Integration

1. Create new Payment Provider class:

```php
namespace App\Payment;

interface PaymentProviderInterface {
    public function initiate(Payment $payment): array;
    public function verify(string $transactionId): bool;
}

class MpesaProvider implements PaymentProviderInterface {
    public function initiate(Payment $payment): array {
        // Call M-Pesa API
        return ['redirect_url' => '...'];
    }

    public function verify(string $transactionId): bool {
        // Verify with M-Pesa API
        return true;
    }
}
```

2. Update Payment model:

```php
public function getProvider() {
    if ($this->provider === 'mpesa') {
        return new MpesaProvider();
    }
    // ... other providers
}
```

### Add Email Notifications

```bash
php artisan make:notification ApplicationApproved
```

Create notification class:

```php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Membership;

class ApplicationApproved extends Notification {
    public $membership;

    public function __construct(Membership $membership) {
        $this->membership = $membership;
    }

    public function via($notifiable) {
        return ['mail'];
    }

    public function toMail($notifiable) {
        return (new MailMessage)
            ->subject('Your CCHPL Membership Application Approved')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your membership application has been approved!')
            ->action('Login to Dashboard', url('/dashboard'))
            ->line('Welcome to CCHPL!');
    }
}
```

### Add New Membership Category

```php
// Programmatically
MembershipCategory::create([
    'name' => 'Partner',
    'annual_fee' => 1500,
    'voting_rights' => false,
    'eligibility_criteria' => 'Commercial partners and suppliers',
    'other_notes' => 'Special pricing available'
]);
```

### Add Members Table Export

```php
Route::get('/admin/members/export', function() {
    $members = Membership::where('status', 'approved')->with('user', 'category')->get();
    
    $csv = "Name,Email,Phone,Category,Expiry Date\n";
    foreach ($members as $m) {
        $csv .= "{$m->user->name},{$m->user->email},{$m->user->phone},{$m->category->name},{$m->expiry_date}\n";
    }
    
    return response($csv, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="members.csv"'
    ]);
});
```

---

## Troubleshooting Guide

### Livewire File Upload Not Working

Check:
1. `WithFileUploads` trait is used
2. `temporary` disk configured in `config/filesystems.php`
3. Storage permissions correct (755)
4. File size limits in `php.ini`
5. Form uses `wire:model` for file inputs

### Validation Errors Not Showing

1. Ensure `#[Validate(...)]` attributes used (Livewire v3)
2. Or use `public function rules()`
3. Check that `wire:model` names match property names
4. Use `@error('field')` in view to display

### Missing Files in Storage

1. Check `php artisan storage:link` was run
2. Verify `storage/app/public` directory exists
3. Set correct permissions: `chmod -R 755 storage`
4. Check upload was successful (check validation)

### Admin Routes Not Accessible

1. Create admin middleware
2. Register in `app/Http/Kernel.php`
3. Implement role checking (use Spatie roles if needed)
4. Verify authenticated user passes admin check

---

**For more help, refer to:**
- Laravel Documentation: https://laravel.com/docs
- Livewire Documentation: https://livewire.laravel.com
- Application logs: `storage/logs/laravel.log`
