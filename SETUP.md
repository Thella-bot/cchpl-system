# CCHPL System - Setup Guide

Complete step-by-step guide to set up the CCHPL Membership & Payment Management System.

## Prerequisites

Ensure you have the following installed on your system:

- **PHP** 8.1 or higher
- **Composer** (PHP package manager)
- **MySQL/PostgreSQL** database server
- **Node.js** 14+ (for Laravel Mix)
- **npm** or **yarn** (for frontend dependencies)

Verify installations:
```bash
php --version
composer --version
mysql --version
node --version
npm --version
```

## Step 1: Clone/Setup Project

If cloning from repository:
```bash
git clone <repository-url> cchpl-system
cd cchpl-system
```

Or if you have the project files:
```bash
cd cchpl-system
```

## Step 2: Install Dependencies

Install PHP dependencies via Composer:
```bash
composer install
```

Install frontend dependencies:
```bash
npm install
```

## Step 3: Environment Configuration

Copy the example environment file:
```bash
cp .env.example .env
```

Edit `.env` with your settings:
```env
# Using MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cchpl_system
DB_USERNAME=root
DB_PASSWORD=

# Using PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=cchpl_system
DB_USERNAME=postgres
DB_PASSWORD=

# App Configuration
APP_NAME="CCHPL System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Mail Configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@cchpl.ls

# Payment Providers
MPESA_SHORTCODE=123456
ECOCASH_MERCHANT=264123456
```

## Step 4: Generate Application Key

```bash
php artisan key:generate
```

This creates an encryption key for your application.

## Step 5: Create Database

Create a new database in your MySQL/PostgreSQL server:

**MySQL**:
```sql
CREATE DATABASE cchpl_system;
```

**PostgreSQL**:
```sql
CREATE DATABASE cchpl_system;
```

## Step 6: Run Database Migrations

```bash
php artisan migrate
```

This creates all necessary tables:
- users
- membership_categories
- memberships
- payments
- membership_documents

## Step 7: Seed Initial Data

```bash
php artisan db:seed --class=MembershipCategorySeeder
```

This populates the membership categories:
- Professional (M400/year)
- Associate (M250/year)
- Student/Trainee (M100/year)
- Corporate/Institutional (M2000/year)
- Honorary (Free)

## Step 8: Create Storage Link

Make uploaded files accessible via web:
```bash
php artisan storage:link
```

This creates a symbolic link from `storage/app/public` to `public/storage`.

## Step 9: Set Directory Permissions

Ensure storage and bootstrap directories are writable:

**Linux/Mac**:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

**Windows**: 
Directory permissions are typically handled by ownership (run as administrator if needed).

## Step 10: Build Frontend Assets

Compile CSS and JavaScript:
```bash
npm run dev     # Development
npm run build   # Production
```

Or for development with file watching:
```bash
npm run watch
```

## Step 11: Create Admin User (Manual)

For now, create an admin user via tinker:

```bash
php artisan tinker
```

Then in the tinker shell:
```php
$user = new App\Models\User();
$user->name = "Admin User";
$user->email = "admin@cchpl.ls";
$user->password = bcrypt("password123");
$user->save();
exit
```

*Note: Later implement a registration flow or migration*

## Step 12: Start Development Server

```bash
php artisan serve
```

The application will be available at: **http://localhost:8000**

## Step 13: Create Admin Middleware

Create or update `app/Http/Middleware/AdminMiddleware.php`:

```php
<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware {
    public function handle(Request $request, Closure $next) {
        // TODO: Add admin role check (requires additional setup)
        // For now, assumes authenticated user
        if (!auth()->check()) {
            return redirect('login');
        }
        return $next($request);
    }
}
```

Register in `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    // ... existing middleware
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];
```

## Testing the System

### 1. User Registration & Membership Application
```
1. Visit http://localhost:8000/register
2. Create a new account
3. Go to http://localhost:8000/membership/apply
4. Select a category and upload documents
5. Submit application
```

### 2. Payment Processing
```
1. After application approval (via admin)
2. Go to http://localhost:8000/payment/initiate
3. Select payment provider
4. Enter amount and generate reference
5. Upload payment proof screenshot
```

### 3. Admin Dashboard
```
1. Login as admin user
2. Go to http://localhost:8000/admin/memberships/pending
3. Review applications and approve/reject
4. Go to http://localhost:8000/admin/payments/pending
5. Verify payment proofs
6. View all members at http://localhost:8000/admin/memberships/all
```

## Configuration After Setup

### Email Notifications

Update `config/mail.php` and `.env` with your email provider:

Supported services:
- **Mailtrap** (testing)
- **Gmail** (SMTP)
- **Postmark**
- **SendGrid**
- **SES (AWS)**

### Implement Email Notifications

Create notification classes:

```bash
php artisan make:notification ApplicationReceived
php artisan make:notification ApplicationApproved
php artisan make:notification PaymentReceived
php artisan make:notification PaymentVerified
```

Update models to send notifications:

```php
// In Membership model
protected static function booted() {
    static::created(function ($membership) {
        $membership->user->notify(new ApplicationReceived($membership));
    });
}
```

### Add Role/Admin System (Optional)

For a complete role-based system, consider:

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

Then update middleware to check roles.

## Production Deployment

### Before Going Live

1. Set `APP_DEBUG=false` in `.env`
2. Set `APP_ENV=production`
3. Ensure `APP_KEY` is set (run `php artisan key:generate`)
4. Update mail configuration with production service
5. Enable HTTPS
6. Set proper database backups

### Database Optimization

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### Background Jobs (Optional)

If using queued email notifications:

```bash
php artisan queue:work
```

Or use supervisor for production.

## Troubleshooting

### "No application encryption key has been specified"
```bash
php artisan key:generate
```

### "SQLSTATE[HY000]: General error"
Ensure database exists and credentials are correct in `.env`

### "No such file or directory" for storage
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

### Livewire form not submitting
1. Clear browser cache (Ctrl+Shift+Delete)
2. Ensure `@livewireStyles` and `@livewireScripts` in layout
3. Check browser console for JS errors

### Files not uploading
1. Ensure `storage/app/public` directory exists
2. Check write permissions on storage directory
3. Verify file size limits in php.ini and `.env`

### CSS/JS not loading
```bash
npm run dev
php artisan serve
```

## Getting Help

- Check Laravel documentation: https://laravel.com/docs
- Check Livewire documentation: https://livewire.laravel.com
- Review application logs: `storage/logs/laravel.log`

## Next Steps

1. Implement email notifications
2. Add user profile management
3. Create membership renewal system
4. Add payment history reports
5. Implement member directory search
6. Add SMS notifications for payments
7. Create API for mobile app
8. Implement audit logging

---

**Setup completed!** Your CCHPL System is ready to use. 🎉
