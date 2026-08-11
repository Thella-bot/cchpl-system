# ⚙️ CCHPL Middleware Configuration

How the security middleware works and how to set it up.

---

## 🔒 What Is Middleware?

Middleware is like a security guard at the door. Every time someone tries to visit a page, the middleware checks if they're allowed in.

```
User Request → Middleware Checks → Allowed? → Show Page
                  ↓
               Not Allowed? → Show Error / Redirect
```

---

## 🛡️ Our Middleware

### 1. AdminMiddleware (`app/Http/Middleware/AdminMiddleware.php`)

**What it checks:** Is the user logged in AND marked as an admin?

**When to use:** Any admin page.

**What happens if they fail:** Redirected to login page.

```php
// In routes
Route::middleware('admin')->group(function () {
    // Only logged-in admins can see these pages
});
```

---

### 2. SuperAdminMiddleware (`app/Http/Middleware/SuperAdminMiddleware.php`)

**What it checks:** Is the user a Super Admin?

**When to use:** Pages that only the boss should see.

**What happens if they fail:** "Access Denied" error.

```php
// In routes
Route::middleware('super-admin')->group(function () {
    // Only Super Admins can see these pages
});
```

**Examples of protected pages:**
- Dashboard with all stats
- Admin management
- Audit logs
- Role management

---

### 3. RoleMiddleware (`app/Http/Middleware/RoleMiddleware.php`)

**What it checks:** Does the user have ANY of the specified roles?

**When to use:** Pages that need specific expertise.

**What happens if they fail:** "Access Denied" error.

```php
// In routes - single role
Route::middleware('role:membership_admin')->group(function () {
    // Only Membership Admins
});

// In routes - multiple roles (ANY match)
Route::middleware('role:membership_admin,payment_admin')->group(function () {
    // Membership Admin OR Payment Admin
});

// In routes - always include super_admin
Route::middleware('role:membership_admin,super_admin')->group(function () {
    // Membership Admin OR Super Admin
});
```

**Important:** Always include `super_admin` in role lists so the boss can access everything.

---

## 📝 How to Register Middleware

### Step 1: Open the Kernel File

Open: `app/Http/Kernel.php`

### Step 2: Add to `$routeMiddleware`

Find this section:
```php
protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    // ... other middleware ...
];
```

Add these three lines:
```php
protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    // ... other middleware ...

    // CCHPL Custom Middleware
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'super-admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
```

### Step 3: Clear Cache

```bash
php artisan route:cache
php artisan config:cache
```

---

## 🗺️ How Routes Use Middleware

### Real Example from `routes/web.php`

```php
// All admin routes require: login + is_admin flag
