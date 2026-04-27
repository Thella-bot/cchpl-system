# ⚡ CCHPL Admin Quick Reference

Your cheat sheet for common admin tasks. Keep this handy!

---

## 👥 Admin Roles at a Glance

| Role | What They Do | Pages They Can Access |
|------|-------------|----------------------|
| 👑 **Super Admin** | Everything | All admin pages |
| 📋 **Membership Admin** | Review applications | `/admin/memberships/*`, `/admin/resignations/*` |
| 💰 **Payment Admin** | Verify payments | `/admin/payments/*` |
| 📊 **Reports Admin** | View stats & exports | `/admin/reports/*` |
| 🏦 **Finance Admin** | Manage fees | `/admin/memberships/categories/*` |
| 📝 **Content Admin** | (Reserved for future) | — |

> 💡 **Tip:** Super Admin can access everything. Other roles only see their own sections.

---

## 🚀 Quick Setup (5 Steps)

### 1. Create Database Tables
```bash
php artisan migrate
```

### 2. Add Default Roles
```bash
php artisan db:seed --class=RoleSeeder
```

### 3. Register Middleware (One-time setup)
Add these three lines to `app/Http/Kernel.php` in the `$routeMiddleware` array:
```php
'admin' => \App\Http\Middleware\AdminMiddleware::class,
'super-admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
'role' => \App\Http\Middleware\RoleMiddleware::class,
```

### 4. Create Your First Super Admin
```bash
php artisan tinker
```
Then:
```php
App\Services\AdminService::createSuperAdmin([
    'name' => 'Admin Name',
    'email' => 'admin@cchpl.ls',
    'password' => 'StrongPassword123!'
]);
exit
```

### 5. Log In and Test
Go to `/admin/dashboard` and log in.

---

## 🛠️ Common Admin Tasks

### Create a New Admin

**Via Code (Tinker):**
```bash
php artisan tinker
```
```php
App\Services\AdminService::createAdmin([
    'name' => 'Jane Smith',
    'email' => 'jane@cchpl.ls',
    'password' => 'SecurePassword123!',
    'roles' => [2] // 2 = membership_admin
]);
```

**Role ID Quick Reference:**
| ID | Role Name |
|----|-----------|
| 1 | super_admin |
| 2 | membership_admin |
| 3 | payment_admin |
| 4 | reports_admin |
| 5 | finance_admin |
| 6 | content_admin |

**Create Multi-Role Admin:**
```php
App\Services\AdminService::createAdmin([
    'name' => 'Multi Admin',
    'email' => 'multi@cchpl.ls',
    'password' => 'password',
    'roles' => [2, 3, 4] // membership + payment + reports
]);
```

---

### Add or Remove Roles from an Admin

```php
$user = App\Models\User::find(5);

// Add a role
$user->assignRole('reports_admin');

// Remove a role
$user->removeRole('payment_admin');

// Replace all roles
$user->roles()->sync([2, 4]); // membership_admin + reports_admin
```

---

### Deactivate an Admin

```php
$user = App\Models\User::find(5);
App\Services\AdminService::revokeAdminAccess($user);
```

This removes all roles and sets `is_admin` to `false`.

---

### Check What Roles a User Has

```php
$user = App\Models\User::find(5);

// Simple checks
echo $user->isAdmin() ? 'Yes' : 'No';               // Is any admin?
echo $user->isSuperAdmin() ? 'Yes' : 'No';          // Is super admin?
echo $user->hasRole('membership_admin') ? 'Yes' : 'No'; // Specific role?

// List all roles
echo $user->roles->pluck('name')->implode(', ');
```

---

## 🌐 Admin Route Quick List

### Super Admin Only
```
/admin/dashboard          → Dashboard with stats
/admin/admins             → List all admins
/admin/admins/{user}      → View admin details
/admin/audit-log          → System activity log
/admin/roles              → Manage roles
```

### Membership Admin
```
/admin/memberships/pending         → Review new applications
/admin/memberships/{id}            → View application details
/admin/memberships/list/all        → All members
/admin/memberships/list/rejected   → Rejected applications
/admin/resignations                → Member resignations
```

### Payment Admin
```
/admin/payments/pending       → Payments waiting for verification
/admin/payments/{id}          → View payment details
/admin/payments/list/verified → Approved payments
/admin/payments/list/rejected → Rejected payments
```

### Reports Admin
```
/admin/reports/                → Reports dashboard
/admin/reports/memberships     → Membership statistics
/admin/reports/payments        → Payment statistics
/admin/reports/export/members  → Download members CSV
/admin/reports/export/payments → Download payments CSV
```

### Finance Admin
```
/admin/memberships/categories              → View categories & fees
/admin/memberships/categories/{id}/edit    → Edit category fees
```

---

## 🔧 Middleware Quick Reference

```php
// Any logged-in admin
Route::middleware('admin')->group(function () { ... });

// Only Super Admins
Route::middleware('super-admin')->group(function () { ... });

// Specific role required
Route::middleware('role:membership_admin')->group(function () { ... });

// Any of multiple roles
Route::middleware('role:membership_admin,payment_admin')->group(function () { ... });
```

---

## 🆘 Quick Fixes

### Admin Can't Log In
```php
$user = App\Models\User::where('email', 'admin@cchpl.ls')->first();
$user->update(['is_admin' => true]);
$user->assignRole('super_admin');
```

### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Check If Middleware Is Working
```php
// In any controller or tinker
auth()->user()->isAdmin();
auth()->user()->hasRole('membership_admin');
auth()->user()->roles->pluck('name');
```

---

## 📂 Where Files Live

```
Controllers:     app/Http/Controllers/Admin/
Middleware:      app/Http/Middleware/
Services:        app/Services/AdminService.php
Models:          app/Models/User.php, app/Models/Role.php
Seeders:         database/seeders/RoleSeeder.php
Views:           resources/views/admin/
Routes:          routes/web.php
```

---

## 📚 Full Documentation

| Guide | What's Inside |
|-------|---------------|
| [ADMIN_ROLES_GUIDE.md](ADMIN_ROLES_GUIDE.md) | Detailed role explanations |
| [ADMIN_SYSTEM_REFACTORING.md](ADMIN_SYSTEM_REFACTORING.md) | Technical architecture |
| [KERNEL_CONFIGURATION.md](KERNEL_CONFIGURATION.md) | Middleware setup details |
| [DEVELOPER_REFERENCE.md](DEVELOPER_REFERENCE.md) | Code examples |

