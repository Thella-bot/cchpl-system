# CCHPL Admin System - Quick Reference Card

## Admin Roles at a Glance

| Role | Abilities | Access |
|------|-----------|--------|
| **Super Admin** (root) | EVERYTHING | Full system access |
| **Membership Admin** | Review & approve/reject applications | `/admin/memberships/*` |
| **Payment Admin** | Verify payment proofs | `/admin/payments/*` |
| **Reports Admin** | View stats & export data | `/admin/reports/*` |
| **Finance Admin** | Update membership fees & pricing | `/admin/memberships/categories*` |
| **Content Admin** | Manage system content | Reserved |

---

## Quick Setup (5 Steps)

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Seed Roles**
   ```bash
   php artisan db:seed --class=RoleSeeder
   ```

3. **Register Middleware** (in `app/Http/Kernel.php`)
   ```php
   'admin' => \App\Http\Middleware\AdminMiddleware::class,
   'super-admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
   'role' => \App\Http\Middleware\RoleMiddleware::class,
   ```

4. **Create Super Admin**
   ```bash
   php artisan tinker
   AdminService::createSuperAdmin(['name' => 'Admin', 'email' => 'admin@cchpl.ls', 'password' => 'root_cchpl'])
   ```

5. **Test Login**
   Navigate to `/admin/dashboard` and login

---

## Common Commands

### Create Admin Users

```php
// Super Admin
AdminService::createSuperAdmin([
    'name' => 'John Doe',
    'email' => 'john@cchpl.ls',
    'password' => 'password123'
]);

// Membership Admin
AdminService::createAdmin([
    'name' => 'Jane Smith',
    'email' => 'jane@cchpl.ls',
    'password' => 'password123',
    'roles' => [Role::where('name', 'membership_admin')->first()->id]
]);

// Multi-Role Admin
AdminService::createAdmin([
    'name' => 'Multi Admin',
    'email' => 'multi@cchpl.ls',
    'password' => 'password123',
    'roles' => [2, 3, 4] // membership, payment, reports
]);
```

### Manage Roles

```php
$user = User::find(1);

// Add role
$user->assignRole('payment_admin');

// Remove role
$user->removeRole('payment_admin');

// Replace all roles
$user->roles()->sync([Role::where('name', 'membership_admin')->first()->id]);

// Check role
$user->hasRole('super_admin'); // bool
$user->isSuperAdmin();          // bool
$user->isAdmin();               // bool
```

### Deactivate Admin

```php
// Remove all admin access
AdminService::revokeAdminAccess($user);
```

---

## Admin Routes

### Super Admin Only
```
/admin/dashboard               - Dashboard
/admin/admins                 - List admins
/admin/admins/{user}          - View admin
/admin/audit-log              - Audit log
/admin/roles                  - Manage roles
```

### Membership Admin
```
/admin/memberships/pending    - Pending apps
/admin/memberships/{id}       - View app
/admin/memberships/list/all   - All members
/admin/memberships/list/rejected - Rejected
```

### Payment Admin
```
/admin/payments/pending       - Pending payments
/admin/payments/{id}          - View payment
/admin/payments/list/verified - Verified
/admin/payments/list/rejected - Rejected
```

### Reports Admin
```
/admin/reports/               - Dashboard
/admin/reports/memberships    - Stats
/admin/reports/payments       - Payment stats
/admin/reports/export/members - Export CSV
/admin/reports/export/payments - Export CSV
```

---

## Middleware Usage

```php
// Require admin
Route::middleware('admin')->group(function () { ... });

// Require super admin
Route::middleware('super-admin')->group(function () { ... });

// Require specific role
Route::middleware('role:membership_admin')->group(function () { ... });

// Require any role
Route::middleware('role:membership_admin,payment_admin')->group(function () { ... });
```

---

## User Model Methods

```php
$user = User::find(1);

// Check status
$user->isAdmin()               // Is any type of admin?
$user->isSuperAdmin()          // Is super admin?
$user->hasRole('membership_admin')        // Has specific role?
$user->hasAnyRole(['membership_admin', 'payment_admin']) // Has any of these?

// Manage roles
$user->roles                   // Get all roles
$user->assignRole('payment_admin')
$user->removeRole('payment_admin')
```

---

## Troubleshooting

### Admin can't access dashboard

1. Check `is_admin` is `true`:
   ```php
   User::find(1)->is_admin // Should be 1
   ```

2. Check has roles:
   ```php
   User::find(1)->roles->count() // Should be > 0
   ```

3. Clear cache:
   ```bash
   php artisan route:cache
   php artisan config:cache
   ```

### Middleware not working

1. Register in `app/Http/Kernel.php` ✓
2. Clear route cache: `php artisan route:cache`
3. Restart server

### "User is not an admin" error

```php
$user = User::find(1);
$user->update(['is_admin' => true]);
$user->assignRole('membership_admin');
```

---

## Database Tables

**roles** - Admin roles
```sql
id | name | display_name | description
1  | super_admin | Super Administrator | Full access
2  | membership_admin | Membership Admin | ...
```

**user_roles** - User to role mapping
```sql
id | user_id | role_id
1  | 5       | 2
```

**users** (updated)
```sql
... | is_admin | last_login_at
1   | true     | 2024-01-15 10:30:00
```

---

## File Locations

```
Controllers:  app/Http/Controllers/Admin/
              ├── MembershipAdminController.php
              ├── PaymentAdminController.php
              ├── SuperAdminController.php
              └── ReportsController.php

Middleware:   app/Http/Middleware/
              ├── AdminMiddleware.php
              ├── SuperAdminMiddleware.php
              └── RoleMiddleware.php

Services:     app/Services/
              └── AdminService.php

Models:       app/Models/
              ├── User.php (updated)
              └── Role.php

Seeders:      database/seeders/
              ├── RoleSeeder.php
              └── MembershipCategorySeeder.php

Migrations:   database/migrations/
              ├── *_create_roles_table.php
              ├── *_create_user_roles_table.php
              └── *_add_admin_fields_to_users_table.php

Routes:       routes/web.php (updated)

Docs:         ADMIN_ROLES_GUIDE.md
              KERNEL_CONFIGURATION.md
```

---

## Best Practices

✅ Create 1-2 super admins only
✅ Use least privilege principle
✅ Separate duties between admins
✅ Review admin roles quarterly
✅ Monitor admin activities
✅ Change default passwords
❌ Don't share credentials
❌ Don't make everyone super admin
❌ Don't use root for routine tasks

---

**For complete documentation, see:** [ADMIN_ROLES_GUIDE.md](ADMIN_ROLES_GUIDE.md)
