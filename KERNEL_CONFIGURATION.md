# Kernel.php Configuration

This file shows the middleware that needs to be registered in `app/Http/Kernel.php`

## Location

Edit: `app/Http/Kernel.php`

## Add to `$routeMiddleware` array

```php
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     */
    protected $middleware = [
        // ... existing middleware
    ];

    /**
     * The application's route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            // ... existing middleware
        ],

        'api' => [
            // ... existing middleware
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     */
    protected $routeMiddleware = [
        // ... existing middleware

        // ADD THESE THREE:
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'super-admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ];
}
```

## Usage in Routes

After adding the middleware to Kernel.php, you can use them in routes:

```php
// Check if user is an admin
Route::middleware('admin')->group(function () { ... });

// Check if user is super admin
Route::middleware('super-admin')->group(function () { ... });

// Check if user has specific role
Route::middleware('role:membership_admin')->group(function () { ... });

// Check if user has any of multiple roles
Route::middleware('role:membership_admin,payment_admin')->group(function () { ... });
```

## Testing

The routes are already configured in `routes/web.php` with these middleware.

To verify it's working:

```php
// In tinker
php artisan tinker

// Create a test user
$user = User::create([
    'name' => 'Test Admin',
    'email' => 'test@admin.com',
    'password' => bcrypt('password'),
    'is_admin' => true
]);

// Assign a role
$user->assignRole('membership_admin');

// Try accessing admin routes
/admin/memberships/pending
```
