# CCHPL System - Admin Role Management Guide

Complete guide to the multi-admin, role-based system with Super Admin (root user) capabilities.

## System Overview

The CCHPL System implements a hierarchical role-based access control (RBAC) system:

```
┌─────────────────────────────────────────────┐
│         SUPER ADMIN (Root User)             │
│  ✓ Full system access                       │
│  ✓ Manage all admins                        │
│  ✓ Create/edit roles                        │
│  ✓ View all features                        │
│  ✓ Audit logs & system settings             │
└─────────────────────────────────────────────┘
           ↓ Can delegate to ↓
    ┌──────────┬──────────┬──────────┐
    │          │          │          │
┌───────┐ ┌───────┐ ┌──────────┐ ┌─────────┐
│ Memb. │ │Payment│ │ Reports  │ │ Content │
│ Admin │ │ Admin │ │  Admin   │ │ Admin   │
└───────┘ └───────┘ └──────────┘ └─────────┘
```

---

## Admin Roles

### 1. Super Administrator (super_admin)

**Root user with complete system access**

Permissions:
- ✅ View admin dashboard with all statistics
- ✅ Create new admin accounts
- ✅ Assign/modify admin roles
- ✅ Deactivate admin accounts
- ✅ View and filter all memberships
- ✅ View and verify all payments
- ✅ Access all reports
- ✅ Manage system roles
- ✅ View audit logs
- ✅ Process all membership applications
- ✅ Export all data

Routes:
```
/admin/dashboard                    # Super admin dashboard
/admin/admins                       # List all admins
/admin/admins/{user}               # View admin details
/admin/audit-log                   # System audit log
/admin/roles                       # Manage roles
```

### 2. Membership Administrator (membership_admin)

**Handles membership application reviews and approvals**

Permissions:
- ✅ View pending applications list
- ✅ Review application details & documents
- ✅ Approve/reject applications
- ✅ View all members
- ✅ Filter members by status
- ✅ View rejected applications

❌ Cannot:
- Verify payments
- View reports
- Manage other admins
- Export data

Routes:
```
/admin/memberships/pending         # Pending applications
/admin/memberships/{membership}    # View application
/admin/memberships/list/all        # All members
/admin/memberships/list/rejected   # Rejected applications
```

### 3. Payment Administrator (payment_admin)

**Handles payment verification and reconciliation**

Permissions:
- ✅ View pending payments
- ✅ Review payment proof images
- ✅ Verify/reject payments
- ✅ View verified payments
- ✅ View rejected payments
- ✅ Add verification notes

❌ Cannot:
- Manage memberships
- View reports
- Manage other admins
- Export data

Routes:
```
/admin/payments/pending            # Pending payment verification
/admin/payments/{payment}          # View payment details
/admin/payments/list/verified      # Verified payments
/admin/payments/list/rejected      # Rejected payments
```

### 4. Finance Administrator (finance_admin)

**Updates membership fees and financial settings**

Permissions:
- ✅ Update membership category fees
- ✅ Adjust membership category descriptions and eligibility notes
- ✅ Trigger fee change audit logs

❌ Cannot:
- Review membership applications
- Verify payments
- Manage other admins
- View sensitive reports (unless also assigned reports_admin)

Routes:
```
/admin/memberships/categories      # List membership categories and fees
/admin/memberships/categories/{category}/edit  # Edit category fee/details
```

### 5. Reports Administrator (reports_admin)

**Handles reporting and data exports**

Permissions:
- ✅ View membership statistics
- ✅ View payment statistics
- ✅ Generate monthly revenue reports
- ✅ Export member list (CSV)
- ✅ Export payment records (CSV)
- ✅ View expiring memberships
- ✅ Track revenue by provider

❌ Cannot:
- Manage memberships
- Verify payments
- Manage other admins

Routes:
```
/admin/reports/                    # Main reports dashboard
/admin/reports/memberships         # Membership statistics
/admin/reports/payments            # Payment statistics
/admin/reports/export/members      # Export members CSV
/admin/reports/export/payments     # Export payments CSV
```

### 5. Content Administrator (content_admin - Reserved)

**For future use: manage categories, content, etc**

---

## Database Schema

### Roles Table
```sql
CREATE TABLE roles (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) UNIQUE,          -- 'super_admin', 'membership_admin', etc
    display_name VARCHAR(255),         -- 'Super Administrator'
    description TEXT,                  -- Description of the role
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### User Roles Junction Table
```sql
CREATE TABLE user_roles (
    id BIGINT PRIMARY KEY,
    user_id BIGINT FOREIGN KEY,        -- Reference to users table
    role_id BIGINT FOREIGN KEY,        -- Reference to roles table
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Users Table (Updated)
```sql
ALTER TABLE users ADD COLUMN is_admin BOOLEAN DEFAULT false;
ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP;
```

---

## User Model Methods

### Check Admin Status

```php
$user = User::find(1);

// Check if user is admin
if ($user->isAdmin()) {
    // User is an admin with at least one role
}

// Check if user is super admin
if ($user->isSuperAdmin()) {
    // User is super admin
}

// Check if user has specific role
if ($user->hasRole('membership_admin')) {
    // User is membership admin
}

// Check if user has any of the roles
if ($user->hasAnyRole(['membership_admin', 'payment_admin'])) {
    // User has at least one of these roles
}
```

### Manage Roles

```php
// Assign role
$user->assignRole('membership_admin');

// Remove role
$user->removeRole('membership_admin');

// Get all roles
$roles = $user->roles; // Collection of Role models

// Assign multiple roles (sync)
$user->roles()->sync([1, 3, 5]); // By role IDs
```

---

## AdminService Helper Class

Complete helper class for managing admins programmatically.

### Create Admin User

```php
use App\Services\AdminService;

$newAdmin = AdminService::createAdmin([
    'name' => 'John Doe',
    'email' => 'john@cchpl.ls',
    'password' => 'secure_password_123',
    'phone' => '+266 12345678',
    'roles' => [2, 3]  // role IDs for membership and payment admin
]);
```

### Create Super Admin

```php
$superAdmin = AdminService::createSuperAdmin([
    'name' => 'Root Admin',
    'email' => 'admin@cchpl.ls',
    'password' => 'super_secure_password',
    'phone' => '+266 98765432'
]);
```

### Get All Admins

```php
$admins = AdminService::getAllAdmins();
// Returns all admin users with their roles loaded
```

### Get Admins by Role

```php
$membershipAdmins = AdminService::getAdminsByRole('membership_admin');
$paymentAdmins = AdminService::getAdminsByRole('payment_admin');
```

### Check Permissions

```php
if (AdminService::canPerformAction($user, 'approve_applications')) {
    // User can approve applications
}

if (AdminService::canPerformAction($user, 'verify_payments')) {
    // User can verify payments
}
```

### Revoke Admin Access

```php
AdminService::revokeAdminAccess($user);
// Removes admin flag and all roles
```

---

## Middleware

### Admin Middleware

Checks if user is authenticated and is an admin:

```php
Route::middleware('admin')->group(function () {
    // Only admins can access these routes
});
```

### Super Admin Middleware

Checks if user is super admin:

```php
Route::middleware('super-admin')->group(function () {
    // Only super admins can access
});
```

### Role Middleware

Checks if user has specific role(s):

```php
Route::middleware('role:membership_admin,payment_admin')->group(function () {
    // Users must have at least one of these roles
});

// Super admin bypasses role checks automatically
```

---

## Route Protection

All admin routes are protected by middleware in the following structure:

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // All routes here require: authenticated + is_admin = true + at least one role

    Route::middleware('super-admin')->group(function () {
        // Super admin only routes
    });

    Route::middleware('role:membership_admin,super_admin')->group(function () {
        // Membership admin and super admin
    });

    Route::middleware('role:payment_admin,super_admin')->group(function () {
        // Payment admin and super admin
    });

    Route::middleware('role:reports_admin,super_admin')->group(function () {
        // Reports admin and super admin
    });
});
```

---

## Setup Instructions

### 1. Run Migrations

```bash
# Create roles and user_roles tables
php artisan migrate

# This runs:
# - 2024_01_01_000005_create_roles_table.php
# - 2024_01_01_000006_create_user_roles_table.php
# - 2024_01_01_000007_add_admin_fields_to_users_table.php
```

### 2. Seed Initial Roles

```bash
php artisan db:seed --class=RoleSeeder
```

This creates 5 system roles:
- `super_admin` - Super Administrator
- `membership_admin` - Membership Administrator
- `payment_admin` - Payment Administrator
- `reports_admin` - Reports Administrator
- `content_admin` - Content Administrator (future use)

### 3. Create First Super Admin

Using Tinker:

```bash
php artisan tinker
```

Then in tinker:

```php
use App\Services\AdminService;

$superAdmin = AdminService::createSuperAdmin([
    'name' => 'Root Administrator',
    'email' => 'admin@cchpl.ls',
    'password' => 'change_me_after_setup',
    'phone' => '+266 XXXXXXXXX'
]);

echo "Super Admin created: {$superAdmin->email}";
exit
```

### 4. Register Middleware in Kernel

Edit `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ... existing middleware
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'super-admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
```

---

## Creating Additional Admins

### Via Super Admin Dashboard

1. Login as Super Admin
2. Go to `/admin/admins`
3. Click "Create New Admin"
4. Fill in details:
   - Name
   - Email
   - Password
   - Phone (optional)
   - Select roles to assign
5. Click "Create Admin"

The new admin will receive email with credentials (TODO: implement email notification).

### Programmatically

```php
use App\Services\AdminService;

// Create membership admin
$membershipAdmin = AdminService::createAdmin([
    'name' => 'Jane Smith',
    'email' => 'jane@cchpl.ls',
    'password' => 'secure_password',
    'roles' => [\App\Models\Role::where('name', 'membership_admin')->first()->id]
]);

// Create multi-role admin
$multiAdmin = AdminService::createAdmin([
    'name' => 'Multi Admin',
    'email' => 'multi@cchpl.ls',
    'password' => 'secure_password',
    'roles' => [
        Role::where('name', 'membership_admin')->first()->id,
        Role::where('name', 'payment_admin')->first()->id,
        Role::where('name', 'reports_admin')->first()->id
    ]
]);
```

---

## Modifying Admin Permissions

### Add Role to Existing Admin

```php
$user = User::find(5);
$user->assignRole('reports_admin');
// or
$user->roles()->attach(Role::where('name', 'reports_admin')->first());
```

### Remove Role from Admin

```php
$user = User::find(5);
$user->removeRole('payment_admin');
// or
$user->roles()->detach(Role::where('name', 'payment_admin')->first());
```

### Replace All Roles

```php
$user = User::find(5);
$user->roles()->sync([
    Role::where('name', 'membership_admin')->first()->id,
    Role::where('name', 'reports_admin')->first()->id
]);
```

---

## Deactivating Admins

### Remove Admin Access

```php
$user = User::find(10);
AdminService::revokeAdminAccess($user);
// Sets is_admin = false
// Removes all roles
```

### Via Super Admin Dashboard

1. Go to `/admin/admins`
2. Find the admin to deactivate
3. Click "Deactivate"
4. Confirm

---

## Best Practices

### Super Admin Security

✅ **DO:**
- Change default password immediately after setup
- Use strong, unique password
- Limit number of super admins (1-2 is recommended)
- Audit admin activities regularly
- Log all administrative actions

❌ **DON'T:**
- Share super admin credentials
- Create super admin accounts for regular users
- Leave default passwords in production
- Grant unnecessary super admin access

### Role Assignment

✅ **DO:**
- Assign least privilege principle
- Give membership admin ONLY membership_admin role (not payment)
- Give payment admin ONLY payment_admin role (not membership)
- Separate duties between admins
- Review admin roles quarterly

❌ **DON'T:**
- Assign multiple unrelated roles to one admin
- Make everyone super admin
- Use root account for routine tasks
- Mix admin and user permissions

### Audit & Monitoring

✅ **DO:**
- Review audit logs weekly
- Track who approved/rejected what
- Monitor payment verifications
- Log all role changes
- Alert on suspicious activity

---

## Troubleshooting

### "User is not an admin" Error

```php
$user = User::find(1);

// Check if admin flag is set
echo $user->is_admin; // Should be 1 (true)

// Check if user has roles
echo $user->roles->count(); // Should be > 0

// Fix: Set admin flag and assign role
$user->update(['is_admin' => true]);
$user->assignRole('membership_admin');
```

### Admin Can't Access Dashboard

1. Check `is_admin` field is `true`
2. Check user has at least one role
3. Check middleware is registered in kernel
4. Check user has required role for specific routes

```php
// Debug
$user = User::find($id);
echo "Is Admin: " . ($user->is_admin ? 'YES' : 'NO');
echo "Roles: " . $user->roles->pluck('name')->join(', ');
echo "Has membership_admin: " . ($user->hasRole('membership_admin') ? 'YES' : 'NO');
```

### Middleware Not Working

1. Register in `app/Http/Kernel.php`:
   ```php
   'admin' => \App\Http\Middleware\AdminMiddleware::class,
   'super-admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
   'role' => \App\Http\Middleware\RoleMiddleware::class,
   ```

2. Clear configuration cache:
   ```bash
   php artisan config:cache
   ```

3. Clear route cache:
   ```bash
   php artisan route:cache
   ```

---

## Admin Controllers Renamed

Old file structure → New file structure:

```
app/Http/Controllers/Admin/
├── MembershipController.php          ✗ DEPRECATED
├── MembershipAdminController.php     ✓ NEW - Membership reviews
├── PaymentAdminController.php        ✓ NEW - Payment verification
├── SuperAdminController.php          ✓ NEW - Super admin dashboard & user management
└── ReportsController.php             ✓ NEW - Reports & exports
```

---

## Environment Configuration

No additional environment variables needed, but ensure:

```env
# In your .env
DB_CONNECTION=mysql              # Database connection
DB_DATABASE=cchpl_system         # Database name

# These already exist
MPESA_SHORTCODE=123456
ECOCASH_MERCHANT=264123456
```

---

## Next Steps

1. ✅ Run migrations
2. ✅ Seed roles
3. ✅ Create super admin
4. ✅ Register middleware in Kernel
5. ✅ Create additional admins
6. ✅ Test role-based access
7. ⏳ Implement email notifications for admin creation
8. ⏳ Add audit logging
9. ⏳ Add activity tracking dashboard

---

For more information, see [DEVELOPER_REFERENCE.md](DEVELOPER_REFERENCE.md)
