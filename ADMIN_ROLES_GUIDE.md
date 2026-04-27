# 👔 CCHPL Admin Roles Guide

Welcome to the CCHPL admin system! This guide explains how roles work, what each admin type can do, and how to manage your team.

---

## 🏢 Admin Hierarchy

Think of the admin system like an organization chart:

```
┌─────────────────────────────────────┐
│      👑 SUPER ADMIN                 │
│   (Full system control)             │
│   • Create other admins             │
│   • View everything                 │
│   • Manage all settings             │
└──────────────┬──────────────────────┘
               │
    ┌──────────┼──────────┬──────────┐
    │          │          │          │
┌───▼───┐ ┌───▼───┐ ┌────▼───┐ ┌───▼────┐
│Membership│ │Payment │ │ Reports│ │Finance │
│  Admin   │ │ Admin  │ │ Admin  │ │ Admin  │
│          │ │        │ │        │ │        │
│• Review  │ │• Verify│ │• Stats │ │• Fees  │
│  apps    │ │  payments│ │• Export│ │• Categories│
│• Approve │ │• Review │ │        │ │        │
│  members │ │  proofs  │ │        │ │        │
└─────────┘ └────────┘ └────────┘ └────────┘
```

---

## 🎭 The Six Admin Roles

### 1. 👑 Super Admin
**The boss. Full access to everything.**

**Can do:**
- View the admin dashboard with all statistics
- Create, edit, and delete admin accounts
- Assign roles to any admin
- View audit logs (who did what and when)
- Access ALL admin sections
- Manage system roles

**Cannot do:**
- Nothing — Super Admins can do everything!

**Best practice:** Have only 1-2 Super Admins. Don't give this role to everyone.

---

### 2. 📋 Membership Admin
**Handles new member applications.**

**Can do:**
- View pending membership applications
- Review application details and uploaded documents
- Approve or reject applications
- View all current members
- View rejected applications
- Handle member resignations

**Cannot do:**
- Verify payments
- View financial reports
- Manage other admins
- Export data

**Typical user:** Membership committee secretary

---

### 3. 💰 Payment Admin
**Verifies that members have paid.**

**Can do:**
- View pending payment proofs
- Review uploaded screenshots/photos of payments
- Approve or reject payments
- Add notes about why a payment was rejected
- View verified and rejected payment history

**Cannot do:**
- Review membership applications
- View reports
- Manage admins

**Typical user:** Finance officer or treasurer

---

### 4. 📊 Reports Admin
**Generates statistics and exports data.**

**Can do:**
- View membership statistics (how many members, by category, etc.)
- View payment statistics (revenue, pending amounts, etc.)
- Export member lists to CSV
- Export payment records to CSV
- Track expiring memberships

**Cannot do:**
- Approve memberships
- Verify payments
- Manage admins

**Typical user:** Secretary or data analyst

---

### 5. 🏦 Finance Admin
**Manages membership fees and pricing.**

**Can do:**
- Update annual fees for membership categories
- Edit category descriptions and eligibility rules
- View fee change history

**Cannot do:**
- Review applications (unless also a Membership Admin)
- Verify payments (unless also a Payment Admin)
- Manage other admins

**Typical user:** Finance committee member

---

### 6. 📝 Content Admin (Reserved)
**For future use.**

This role is reserved for when the system needs someone to manage website content, news, or announcements. It is not currently used.

---

## 🔐 How Role Checking Works

### In the Code
```php
$user = auth()->user();

// Check if user is ANY type of admin
if ($user->isAdmin()) { ... }

// Check if user is Super Admin
if ($user->isSuperAdmin()) { ... }

// Check if user has a specific role
if ($user->hasRole('membership_admin')) { ... }

// Check if user has ANY of these roles
if ($user->hasAnyRole(['membership_admin', 'payment_admin'])) { ... }
```

### Assigning Roles
```php
// Add a role
$user->assignRole('payment_admin');

// Remove a role
$user->removeRole('payment_admin');

// Replace all roles at once
$user->roles()->sync([2, 3]); // Using role IDs
```

---

## 🛡️ Security Best Practices

### ✅ Do This
- Give each admin only the roles they need (least privilege)
- Review admin accounts every 3 months
- Use strong, unique passwords
- Log all admin actions (the system does this automatically)
- Have a backup Super Admin account

### ❌ Don't Do This
- Don't make everyone a Super Admin
- Don't share login credentials
- Don't use the Super Admin account for daily tasks
- Don't forget to remove roles when someone leaves

---

## 🆘 Troubleshooting

### "User is not an admin" Error
Check if the user is flagged as an admin:
```php
$user = App\Models\User::find(1);
echo $user->is_admin; // Should be 1 (true)
```

If not, fix it:
```php
$user->update(['is_admin' => true]);
$user->assignRole('membership_admin');
```

### Admin Can't Access a Page
1. Check they have the right role
2. Check they are marked as `is_admin = true`
3. Clear caches:
   ```bash
   php artisan route:cache
   php artisan config:cache
   ```

### Role Middleware Not Working
Make sure the middleware is registered in `app/Http/Kernel.php`:
```php
'admin' => \App\Http\Middleware\AdminMiddleware::class,
'super-admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
'role' => \App\Http\Middleware\RoleMiddleware::class,
```

---

## 📖 Related Guides

- [ADMIN_QUICK_REFERENCE.md](ADMIN_QUICK_REFERENCE.md) — Quick commands and common tasks
- [ADMIN_SYSTEM_REFACTORING.md](ADMIN_SYSTEM_REFACTORING.md) — Technical details of the role system
- [KERNEL_CONFIGURATION.md](KERNEL_CONFIGURATION.md) — How middleware works

