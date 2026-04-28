# 💻 CCHPL Developer Reference

Code examples and patterns for developers working on the CCHPL system.

---

## 📂 Working with Models

### Users

```php
use App\Models\User;

// Create a regular member
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password'),
    'phone' => '+266 12345678',
]);

// Get all memberships for a user
$memberships = $user->memberships;

// Check if user has an active membership
$hasActive = $user->memberships()
    ->where('status', 'approved')
    ->exists();
```

### Memberships

```php
use App\Models\Membership;

// Create a membership application
$membership = Membership::create([
    'user_id' => $user->id,
    'category_id' => $category->id,
    'status' => 'pending',
]);

// Approve a membership
$membership->update(['status' => 'approved']);
$membership->generateMemberId(); // Creates CCHPL-PRO-2025-001

// Check membership status
if ($membership->isActive()) { ... }
if ($membership->isExpired()) { ... }
if ($membership->isExpiringSoon(30)) { ... }

// Get related data
$membership->user;       // The member
$membership->category;   // Membership category
$membership->payments;   // All payments
$membership->documents;  // Uploaded documents
```

### Payments

```php
use App\Models\Payment;
use App\Services\PaymentService;

// Create a payment record
$payment = Payment::create([
    'membership_id' => $membership->id,
    'amount' => 400.00,
    'provider' => 'mpesa',
    'transaction_reference' => 'CCHPL-20250115-0001',
    'status' => 'pending',
]);

// Verify a payment (extends membership expiry)
PaymentService::verifyPayment($payment, true);

// Check payment status
if ($payment->isVerified()) { ... }
if ($payment->isPending()) { ... }
```

---

## 🧰 Using Services

### PaymentService

```php
use App\Services\PaymentService;

// Generate a unique payment reference
$ref = PaymentService::generateReference();
// Result: CCHPL-20250115-4521

// Get payment instructions text
$instructions = PaymentService::getPaymentInstructions(
    'mpesa',
    '400.00',
    $ref
);

// Calculate late payment penalty (10%)
$penalty = PaymentService::calculatePenalty(400.00);
// Result: 40.00

// Check if membership is overdue for suspension
$shouldSuspend = PaymentService::isOverdueForSuspension($membership);

// Calculate next March expiry date
$expiry = PaymentService::nextMarchExpiry($membership->expiry_date);
```

### MembershipService

```php
use App\Services\MembershipService;

// Generate a member ID
$service = new MembershipService();
$memberId = $service->generateMemberId($membership);
// Result: CCHPL-PRO-2025-001

// Check if penalty applies
$penaltyApplies = $service->isPenaltyApplicable($membership);

// Get category code
$code = MembershipService::categoryCode('Professional Chef');
// Result: PRO
```

### DocumentService

```php
use App\Services\DocumentService;

// Generate a PDF certificate
$pdf = DocumentService::membershipCertificate($membership);
$pdf->download('certificate.pdf');

// Generate a receipt
$pdf = DocumentService::officialReceipt($payment);
$pdf->download('receipt.pdf');

// Generate welcome pack
$pdf = DocumentService::welcomePack($membership);
$pdf->download('welcome-pack.pdf');

// Email a document directly
DocumentService::sendToMember(
    $membership,
    'certificate',
    payment: null,
    subject: 'Your CCHPL Certificate'
);
```

### AdminService

```php
use App\Services\AdminService;

// Create admin
$admin = AdminService::createAdmin([
    'name' => 'Jane Smith',
    'email' => 'jane@cchpl.ls',
    'password' => 'SecurePass123!',
    'roles' => [2] // membership_admin
]);

// Create super admin
$super = AdminService::createSuperAdmin([
    'name' => 'Boss',
    'email' => 'boss@cchpl.ls',
    'password' => 'SuperSecure123!'
]);

// Get all admins
$admins = AdminService::getAllAdmins();

// Get admins by specific role
$paymentAdmins = AdminService::getAdminsByRole('payment_admin');

// Revoke admin access
AdminService::revokeAdminAccess($user);
```

---

## 📝 Audit Logging

Always log important actions:

```php
use App\Models\AuditLog;

AuditLog::create([
    'user_id' => auth()->id(),
    'action' => 'membership.approved',
    'auditable_type' => Membership::class,
    'auditable_id' => $membership->id,
    'old_values' => ['status' => 'pending'],
    'new_values' => ['status' => 'approved'],
    'meta' => ['approved_by' => auth()->user()->email],
]);
```

---

## 🎨 Status Badges

Use the StatusPresenter for consistent UI colors:

```php
use App\Presenters\StatusPresenter;

// In Blade views
<span class="badge {{ StatusPresenter::membershipStatusBadge($membership->status) }}">
    {{ ucfirst($membership->status) }}
</span>

// Available methods
StatusPresenter::membershipStatusBadge('approved');   // bg-success
StatusPresenter::membershipStatusBadge('pending');    // bg-warning text-dark
StatusPresenter::membershipStatusBadge('suspended');  // bg-danger
StatusPresenter::membershipStatusBadge('expired');    // bg-warning text-dark
StatusPresenter::membershipStatusBadge('rejected');   // bg-secondary
StatusPresenter::membershipStatusBadge('resigned');   // bg-secondary

StatusPresenter::paymentStatusBadge('verified');      // bg-success
StatusPresenter::paymentStatusBadge('pending');       // bg-warning text-dark
StatusPresenter::paymentStatusBadge('rejected');      // bg-danger
StatusPresenter::paymentStatusBadge('voided');        // bg-secondary

StatusPresenter::resignationStatusBadge('acknowledged'); // bg-green-100
StatusPresenter::resignationStatusBadge('cancelled');    // bg-gray-100
StatusPresenter::resignationStatusBadge('pending');      // bg-yellow-100
```

---

## 🔄 Livewire Components

### ApplicationForm

Handles membership applications with file uploads:

```php
// Component location
App\Livewire\Membership\ApplicationForm

// Route
Route::get('/membership/apply', ApplicationForm::class)
    ->middleware(['verified', 'throttle:5,1']);
```

### InitiatePayment

Two-step payment workflow:

```php
// Component location
App\Livewire\Payment\InitiatePayment

// Route
Route::get('/payment/initiate', InitiatePayment::class)
    ->middleware(['verified', 'throttle:10,1']);
```

---

## 🛡️ Middleware in Routes

```php
// Basic admin check
Route::middleware('admin')->group(function () {
    // Any admin can access
});

// Super admin only
Route::middleware('super-admin')->group(function () {
    // Only Super Admins
});

// Specific role
Route::middleware('role:membership_admin')->group(function () {
    // Only Membership Admins
});

// Multiple roles (ANY match)
Route::middleware('role:membership_admin,payment_admin')->group(function () {
    // Membership OR Payment Admins
});

// Combined protection
Route::middleware(['auth', 'admin', 'role:payment_admin,super_admin'])
    ->group(function () {
        // Must be: logged in + admin + (payment_admin OR super_admin)
    });
```

---

## 📊 Common Queries

### Dashboard Statistics

```php
// Count pending applications
$pendingApps = Membership::where('status', 'pending')->count();

// Count active members
$activeMembers = Membership::where('status', 'approved')
    ->where('expiry_date', '>', now())
    ->count();

// Total revenue
$revenue = Payment::where('status', 'verified')->sum('amount');

// Pending payments
$pendingPayments = Payment::where('status', 'pending')->count();

// Expiring soon (next 30 days)
$expiringSoon = Membership::where('status', 'approved')
    ->where('expiry_date', '<=', now()->addDays(30))
    ->where('expiry_date', '>=', now())
    ->count();
```

### Member Exports

```php
$members = Membership::where('status', 'approved')
    ->with(['user', 'category'])
    ->get()
    ->map(fn($m) => [
        'name' => $m->user->name,
        'email' => $m->user->email,
        'phone' => $m->user->phone,
        'category' => $m->category->name,
        'member_id' => $m->member_id,
        'expiry' => $m->expiry_date?->format('Y-m-d'),
    ]);
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Unit/Services/PaymentServiceTest.php

# Run with coverage
php artisan test --coverage
```

---

## 🎨 Code Style

```bash
# Check code style
./vendor/bin/pint

# Fix code style automatically
./vendor/bin/pint --fix
```

---

## 📚 Related Documentation

| Document | Purpose |
|----------|---------|
| [ADMIN_ROLES_GUIDE.md](ADMIN_ROLES_GUIDE.md) | Admin role explanations |
| [ADMIN_QUICK_REFERENCE.md](ADMIN_QUICK_REFERENCE.md) | Quick commands |
| [ADMIN_SYSTEM_REFACTORING.md](ADMIN_SYSTEM_REFACTORING.md) | System architecture |
| [KERNEL_CONFIGURATION.md](KERNEL_CONFIGURATION.md) | Middleware details |

