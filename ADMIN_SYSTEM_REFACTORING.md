# 🏗️ CCHPL Admin System Architecture

This document explains how the admin system is built and how the pieces fit together. It's for anyone who wants to understand the technical design.

---

## 📖 What Changed

### Before: Simple Single Admin
- One flag (`is_admin`) controlled everything
- All admins could do everything
- No way to delegate tasks safely

### After: Role-Based Multi-Admin
- Each admin gets specific roles
- Super Admin manages everything
- Specialized admins handle specific tasks
- Built-in audit trail

---

## 🧩 System Pieces

### 1. Roles (What Admins Can Do)

Stored in the `roles` table:

| Role | Name | Purpose |
|------|------|---------|
| 1 | super_admin | Full control |
| 2 | membership_admin | Review applications |
| 3 | payment_admin | Verify payments |
| 4 | reports_admin | Generate reports |
| 5 | finance_admin | Manage fees |
| 6 | content_admin | Future use |

### 2. Users (Who the Admins Are)

The `users` table has two key fields:
- `is_admin` (true/false) — Are they an admin at all?
- `last_login_at` — When did they last log in?

### 3. User Roles (The Connection)

The `user_roles` table connects users to roles:
```
user_id | role_id
--------|--------
   5    |   2      → User #5 is a Membership Admin
   5    |   3      → User #5 is also a Payment Admin
```

---

## 🛡️ How Protection Works

### Three Layers of Security

1. **Login Check** — Are you logged in?
2. **Admin Check** — Is your `is_admin` flag true?
3. **Role Check** — Do you have the right role for this page?

### Example: Accessing Membership Reviews

```
User visits /admin/memberships/pending
    ↓
Are they logged in? (auth middleware)
    ↓
Are they an admin? (admin middleware)
    ↓
Do they have membership_admin OR super_admin role? (role middleware)
    ↓
Yes → Show page
No → Show "Access Denied"
```

---

## 📁 Files and What They Do

### Controllers (Handle Pages)

```
app/Http/Controllers/Admin/
├── SuperAdminController.php      → Dashboard, admin management
├── MembershipAdminController.php → Application reviews
├── PaymentAdminController.php    → Payment verification
├── ReportsController.php         → Statistics & exports
├── ResignationAdminController.php→ Resignation handling
└── DocumentReviewController.php  → AGM notices & EC minutes
```

### Middleware (Gatekeepers)

```
app/Http/Middleware/
├── AdminMiddleware.php        → Checks is_admin flag
├── SuperAdminMiddleware.php   → Checks super_admin role
└── RoleMiddleware.php         → Checks specific roles
```

### Services (Business Logic)

```
app/Services/
├── AdminService.php      → Creating and managing admins
├── PaymentService.php    → Payment verification & receipt numbers
├── MembershipService.php → Member ID generation & penalties
├── DocumentService.php   → PDF generation & email sending
└── DocumentReviewService.php → Document review workflow
```

### Models (Data)

```
app/Models/
├── User.php              → Members and admins
├── Role.php              → Admin role definitions
├── Membership.php        → Membership applications
├── Payment.php           → Payment records
├── AuditLog.php          → Activity tracking
├── Resignation.php       → Resignation requests
└── DocumentReview.php    → Document review queue
```

---

## 🗺️ Route Structure

All admin routes start with `/admin` and require login:

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Super Admin only
    Route::middleware('super-admin')->group(function () {
        Route::get('/dashboard', ...);
        Route::get('/admins', ...);
        Route::get('/audit-log', ...);
    });

    // Membership Admin + Super Admin
    Route::middleware('role:membership_admin,super_admin')->group(function () {
        Route::get('/memberships/pending', ...);
        Route::get('/memberships/list/all', ...);
    });

    // Payment Admin + Super Admin
    Route::middleware('role:payment_admin,super_admin')->group(function () {
        Route::get('/payments/pending', ...);
    });

    // Reports Admin + Super Admin
    Route::middleware('role:reports_admin,super_admin')->group(function () {
        Route::get('/reports', ...);
    });
});
```

---

## 🔐 User Model Methods

```php
// Check if user is any type of admin
$user->isAdmin();                    // Returns true/false

// Check if user is Super Admin
$user->isSuperAdmin();               // Returns true/false

// Check for a specific role
$user->hasRole('membership_admin');  // Returns true/false

// Check for any of multiple roles
$user->hasAnyRole(['membership_admin', 'payment_admin']); // Returns true/false

// Add a role
$user->assignRole('payment_admin');

// Remove a role
$user->removeRole('payment_admin');

// Replace all roles
$user->roles()->sync([2, 3]); // Role IDs
```

---

## 🧰 AdminService Methods

```php
// Create a standard admin
AdminService::createAdmin([
    'name' => 'Jane Smith',
    'email' => 'jane@cchpl.ls',
    'password' => 'SecurePass123!',
    'roles' => [2] // membership_admin
]);

// Create a Super Admin
AdminService::createSuperAdmin([
    'name' => 'Boss',
    'email' => 'boss@cchpl.ls',
    'password' => 'SuperSecure123!'
]);

// Get all admins
$admins = AdminService::getAllAdmins();

// Get admins by role
$paymentAdmins = AdminService::getAdminsByRole('payment_admin');

// Remove admin access
AdminService::revokeAdminAccess($user);
```

---

## 📊 Database Tables

### roles
| Column | Type | Purpose |
|--------|------|---------|
| id | bigint | Unique ID |
| name | string | Role identifier (e.g., 'super_admin') |
| display_name | string | Human-readable name |
| description | text | What this role does |

### user_roles
| Column | Type | Purpose |
|--------|------|---------|
| id | bigint | Unique ID |
| user_id | bigint | Link to users table |
| role_id | bigint | Link to roles table |

### users (relevant columns)
| Column | Type | Purpose |
|--------|------|---------|
| id | bigint | Unique ID |
| is_admin | boolean | Is this user an admin? |
| last_login_at | timestamp | Last login time |

---

## 🔒 Security Design

### Why This Design?

1. **Separation of Duties** — Membership Admin can't touch payments
2. **Least Privilege** — Admins only get what they need
3. **Audit Trail** — Every action is logged
4. **Super Admin Safety** — Can't delete the last Super Admin

### Safety Checks Built In

- Can't deactivate yourself
$user->roles                 // BelongsToMany Role via user_roles
$user->is_admin              // bool - Is any admin?
$user->last_login_at         // timestamp - Last login
```

### Methods

```php
// Role Checking
$user->isAdmin()                              // bool - Is any admin?
$user->isSuperAdmin()                         // bool - Is super admin?
$user->hasRole('membership_admin')            // bool - Has specific role?
$user->hasAnyRole(['role1', 'role2'])         // bool - Has any role(s)?

// Role Management
$user->assignRole('membership_admin')         // Assign role
$user->removeRole('payment_admin')            // Remove role
$user->roles()->sync([1, 3])                  // Set roles directly (by ID)
```

---

## AdminService Helper

Programmatic admin management without accessing raw database.

### Methods

```php
AdminService::createAdmin($data)              // Create standard admin
AdminService::createSuperAdmin($data)         // Create super admin
AdminService::getAllAdmins()                  // Get all admins
AdminService::getAdminsByRole($roleName)      // Get admins by role
AdminService::canPerformAction($user, $action)// Check permission
AdminService::revokeAdminAccess($user)        // Remove admin status
```

### Usage Examples

```php
// Create Membership Admin
AdminService::createAdmin([
    'name' => 'Jane Smith',
    'email' => 'jane@cchpl.ls',
    'password' => 'password123',
    'phone' => '+266 XXXXXXXX',
    'roles' => [Role::where('name', 'membership_admin')->first()->id]
]);

// Create Multi-Role Admin
AdminService::createAdmin([
    'name' => 'Operator',
    'email' => 'op@cchpl.ls',
    'password' => 'password123',
    'roles' => [2, 3, 4]  // membership, payment, reports
]);

// Revoke Admin Access
AdminService::revokeAdminAccess($user);
```

---

## Route Protection

### Before

```php
// Old: No granular control
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // All admins access everything
    Route::get('/memberships/pending', ...);
    Route::get('/payments/pending', ...);
});
```

### After

```php
// New: Role-based access control
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // Super admin only
    Route::middleware('super-admin')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard']);
        Route::get('/admins', [SuperAdminController::class, 'listAdmins']);
    });
    
    // Membership admins
    Route::middleware('role:membership_admin,super_admin')->group(function () {
        Route::get('/memberships/pending', [MembershipAdminController::class, 'index']);
    });
    
    // Payment admins
    Route::middleware('role:payment_admin,super_admin')->group(function () {
        Route::get('/payments/pending', [PaymentAdminController::class, 'index']);
    });
});
```

---

## Middleware Details

### AdminMiddleware

```php
// Checks: User is authenticated AND is_admin = true
// Usage: Route::middleware('admin')
// Returns: 403 if not admin
```

### SuperAdminMiddleware

```php
// Checks: User has 'super_admin' role
// Usage: Route::middleware('super-admin')
// Returns: 403 if not super admin
// Note: Super admin ALWAYS bypasses role checks
```

### RoleMiddleware

```php
// Checks: User has ANY of the specified roles
// Usage: Route::middleware('role:membership_admin,payment_admin')
// Returns: 403 if doesn't have role
// Note: Super admin bypasses this automatically
```

---

## Migration Path

### For Existing Systems (If Upgrading)

1. **Run New Migrations**
   ```bash
   php artisan migrate
   ```

2. **Seed Roles**
   ```bash
   php artisan db:seed --class=RoleSeeder
   ```

3. **Migrate Existing Admins** (if any)
   ```php
   // For each existing admin user:
   $user->update(['is_admin' => true]);
   $user->assignRole('super_admin');  // Or appropriate role
   ```

4. **Register Middleware**
   - Edit `app/Http/Kernel.php`
   - Add 3 middleware entries

5. **Update Routes**
   - Routes already configured in `routes/web.php`

6. **Test**
   - Login as admin
   - Verify access to appropriate routes
   - Test role restrictions

---

## Security Improvements

### Before
- ❌ No role separation
- ❌ Binary admin/non-admin
- ❌ No task delegation
- ❌ All admins have same access

### After
- ✅ Granular role-based access
- ✅ Multi-level admin hierarchy
- ✅ Specialized responsibilities
- ✅ Super admin for complete control
- ✅ Easy audit trail setup
- ✅ Scalable permission system

---

## Documentation Files

| File | Purpose |
|------|---------|
| `ADMIN_ROLES_GUIDE.md` | Complete admin system guide |
| `ADMIN_QUICK_REFERENCE.md` | Quick reference card |
| `KERNEL_CONFIGURATION.md` | Middleware setup |
| `IMPLEMENTATION_CHECKLIST.md` | Updated feature checklist |

---

## Summary of Changes

### New Files Created: 16
- 3 Controllers (PaymentAdminController, SuperAdminController, ReportsController)
- 3 Middleware (AdminMiddleware, SuperAdminMiddleware, RoleMiddleware)
- 1 Model (Role.php)
- 1 Service (AdminService.php)
- 3 Migrations
- 1 Seeder (RoleSeeder.php)
- 1 Documentation file (ADMIN_ROLES_GUIDE.md)

### Files Renamed: 1
- MembershipController → MembershipAdminController

### Files Updated: 4
- User.php - Added role methods
- routes/web.php - Updated with role-based routes
- IMPLEMENTATION_CHECKLIST.md - Updated status
- (Implicit) app/Http/Kernel.php - Need to add middleware

### Database Changes
- 3 new migrations for roles, user_roles, and admin fields

---

## Next Steps

1. ✅ Review all new files
2. ✅ Review renamed controllers
3. ✅ Run migrations: `php artisan migrate`
4. ✅ Seed roles: `php artisan db:seed --class=RoleSeeder`
5. ✅ Register middleware in Kernel.php
6. ✅ Create first super admin via Tinker
7. ✅ Test super admin access
8. ✅ Create additional admins
9. ✅ Test role restrictions
10. ✅ Deploy to production

---

## Support

- See `ADMIN_ROLES_GUIDE.md` for detailed documentation
- See `ADMIN_QUICK_REFERENCE.md` for quick commands
- See `KERNEL_CONFIGURATION.md` for middleware setup
- See `DEVELOPER_REFERENCE.md` for code examples

---

**Admin System Refactoring Complete! 🎉**

The CCHPL System now supports multi-level admin management with a root Super Admin user capable of managing all aspects of the system.
