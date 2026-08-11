# 🚀 CCHPL System Setup Guide

Follow these steps to get the CCHPL Membership System running on your computer or server.

---

## 📋 Before You Start

Make sure you have these installed:

| Requirement | Version | Check Command |
|-------------|---------|---------------|
| PHP | 8.1 or higher | `php --version` |
| Composer | Latest | `composer --version` |
| MySQL or PostgreSQL | Any recent version | `mysql --version` or `psql --version` |
| Node.js | 14 or higher | `node --version` |
| npm | Comes with Node.js | `npm --version` |

---

## 🔧 Step-by-Step Installation

### Step 1: Get the Project Files

If using Git:
```bash
git clone <repository-url> cchpl-system
cd cchpl-system
```

If you already have the files:
```bash
cd cchpl-system
```

---

### Step 2: Install PHP Packages

```bash
composer install
```

This downloads all the PHP libraries the system needs.

---

### Step 3: Install Frontend Packages

```bash
npm install
```

This downloads JavaScript and CSS libraries.

---

### Step 4: Create Your Environment File

```bash
cp .env.example .env
```

Then open `.env` in a text editor and update these settings:

```env
# Application
APP_NAME="CCHPL System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (MySQL example)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cchpl_system
DB_USERNAME=root
DB_PASSWORD=your_password

# Database (PostgreSQL example)
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=cchpl_system
# DB_USERNAME=postgres
# DB_PASSWORD=your_password

# Mail (for email notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@cchpl.ls

# Payment Provider Settings
MPESA_SHORTCODE=your_shortcode
ECOCASH_MERCHANT=your_merchant
```

---

### Step 5: Generate Application Key

```bash
php artisan key:generate
```

This creates a unique security key for your application.

---

### Step 6: Create the Database

**MySQL:**
```bash
mysql -u root -p
```
Then type:
```sql
CREATE DATABASE cchpl_system;
EXIT;
```

**PostgreSQL:**
```bash
psql -U postgres
```
Then type:
```sql
CREATE DATABASE cchpl_system;
\q
```

---

### Step 7: Create Database Tables

```bash
php artisan migrate
```

This creates all the tables the system needs.

---

### Step 8: Add Starting Data

```bash
# Add membership categories (Professional, Associate, Student, etc.)
php artisan db:seed --class=MembershipCategorySeeder

# Add admin roles (Super Admin, Membership Admin, etc.)
php artisan db:seed --class=RoleSeeder
```

---

### Step 9: Set Up File Storage

```bash
php artisan storage:link
```

This lets uploaded files (like payment proofs) be accessible through the web.

---

### Step 10: Set Folder Permissions

**On Linux or Mac:**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

**On Windows:**
Usually not needed, but run your terminal as Administrator if you have permission issues.

---

### Step 11: Build Frontend Files

```bash
# For development (fast, not minified)
npm run dev

# For production (minified, optimized)
npm run build
```

---

### Step 12: Create the First Admin

The first admin should be a **Super Admin** who can manage everything.

```bash
php artisan tinker
```

Then type:
```php
App\Services\AdminService::createSuperAdmin([
    'name' => 'Super Admin',
    'email' => 'admin@cchpl.ls',
    'password' => 'ChangeThisPassword123!',
    'phone' => '+266 XXXXXXXX'
]);
```

Type `exit` to leave tinker.

> ⚠️ **Important:** Change the default password immediately after logging in!

---

### Step 13: Start the Server

```bash
php artisan serve
```

Open your browser and go to **http://localhost:8000**

---

## ✅ Testing Everything Works

### Test Member Registration
1. Go to `/register`
2. Create a new account
3. Log in and visit `/member/dashboard`
4. Try applying for membership at `/membership/apply`

### Test Admin Access
1. Go to `/login`
2. Log in with your Super Admin email and password
3. Visit `/admin/dashboard`
4. Try creating another admin at `/admin/admins`

---

## 🛠️ Common Problems

### "No application encryption key has been specified"
**Fix:**
```bash
php artisan key:generate
```

### "SQLSTATE[HY000]: General error" or "Access denied"
**Fix:** Check your database name, username, and password in `.env`. Make sure the database exists.

### "No such file or directory" for storage
**Fix:**
```bash
php artisan storage:link
```

### Uploaded files not showing
**Fix:**
1. Make sure `storage/app/public` exists
2. Check that the `storage` folder is writable
3. Verify your file upload size limits in `php.ini`

### CSS or JavaScript not loading
**Fix:**
```bash
npm run dev
```

### "User is not an admin" error
**Fix:** Make sure the user has `is_admin = true` and at least one role assigned:
```php
$user = App\Models\User::find(1);
$user->update(['is_admin' => true]);
$user->assignRole('super_admin');
```

---

## 🚀 Going Live (Production)

Before deploying to a real server:

1. **Turn off debug mode**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Use a strong app key** (already generated)

3. **Set up a real mail service** (not Mailtrap)

4. **Enable HTTPS**

5. **Optimize for speed**
   ```bash
   php artisan config:cache
   php artisan route:cache
   composer install --optimize-autoloader --no-dev
   ```

6. **Set up automatic database backups**

7. **Change all default passwords**

---

## 📚 Next Steps

- Read [ADMIN_ROLES_GUIDE.md](ADMIN_ROLES_GUIDE.md) to understand the admin system
- Check [ADMIN_QUICK_REFERENCE.md](ADMIN_QUICK_REFERENCE.md) for day-to-day admin tasks
- See [DEVELOPER_REFERENCE.md](DEVELOPER_REFERENCE.md) if you're customizing the code

---

## 🆘 Still Need Help?

- Check application logs: `storage/logs/laravel.log`
- Visit [Laravel Docs](https://laravel.com/docs)
- Visit [Livewire Docs](https://livewire.laravel.com)

