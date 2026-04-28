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
- Can't remove the last Super Admin
- Role changes are validated
- Admin actions are logged in AuditLog

---

## 📈 Scaling the System

### Adding a New Role

1. Add to `RoleSeeder.php`
2. Add role check in routes
3. Add permission checks in controllers
4. Update documentation

### Adding a New Admin Section

1. Create controller in `app/Http/Controllers/Admin/`
2. Create views in `resources/views/admin/`
3. Add routes with role middleware
4. Add to Super Admin dashboard

---

## 📚 Related Documents

| Document | Focus |
|----------|-------|
| [ADMIN_ROLES_GUIDE.md](ADMIN_ROLES_GUIDE.md) | What each role does |
| [ADMIN_QUICK_REFERENCE.md](ADMIN_QUICK_REFERENCE.md) | Common commands |
| [KERNEL_CONFIGURATION.md](KERNEL_CONFIGURATION.md) | Middleware setup |
| [DEVELOPER_REFERENCE.md](DEVELOPER_REFERENCE.md) | Code examples |

