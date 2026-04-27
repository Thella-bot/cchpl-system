# 🍽️ CCHPL Membership System

Welcome to the **Council for Culinary and Hospitality Professionals Lesotho (CCHPL)** membership management system.

This application handles everything from member sign-ups and payments to admin reviews and reports — all in one place.

---

## ✨ What This System Does

### For Members
- **Apply for membership** online with document uploads
- **Make payments** via M-Pesa or EcoCash
- **Download certificates, receipts, and welcome packs**
- **Submit resignations** when needed
- **View dashboard** with membership status and payment history

### For Admins
- **Review and approve** membership applications
- **Verify payments** with proof uploads
- **Manage admin roles** (Super Admin, Membership Admin, Payment Admin, etc.)
- **Generate reports** and export data
- **Send official documents** like AGM notices and EC minutes
- **Track everything** with audit logs

---

## 🛠️ Technology Stack

| Technology | Purpose |
|------------|---------|
| PHP 8.1 | Backend language |
| Laravel 10 | Web framework |
| Livewire 3 | Interactive UI components |
| Laravel Sanctum | API authentication |
| DomPDF | PDF document generation |
| PHPUnit 10 | Automated testing |
| Laravel Pint | Code formatting |

---

## 🚀 Quick Start

### 1. Install Dependencies
```bash
composer install
```

### 2. Set Up Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database and mail settings.

### 3. Set Up Database
```bash
php artisan migrate
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=MembershipCategorySeeder
```

### 4. Create Storage Link
```bash
php artisan storage:link
```

### 5. Start the Application
```bash
php artisan serve
```

Visit **http://localhost:8000** in your browser.

---

## 👤 Member Features

| Feature | Route | Description |
|---------|-------|-------------|
| Dashboard | `/member/dashboard` | View membership status and payments |
| Profile | `/member/profile` | Update personal details |
| Apply | `/membership/apply` | Submit membership application |
| Pay | `/payment/initiate` | Make membership payments |
| Certificate | `/documents/certificate/{id}` | Download membership certificate |
| Receipt | `/documents/receipt/{id}` | Download payment receipt |
| Welcome Pack | `/documents/welcome-pack/{id}` | Download welcome pack |
| Resign | `/member/resign` | Submit resignation |

---

## 🔐 Admin Features

All admin routes are under `/admin` and require login.

### Super Admin (Full Access)
- Dashboard with system overview
- Create and manage admin accounts
- Assign roles to admins
- View audit logs
- Manage system roles

### Membership Admin
- Review pending applications
- Approve or reject memberships
- View all members
- Handle resignations

### Payment Admin
- Verify payment proofs
- Approve or reject payments
- View payment history

### Finance Admin
- Update membership fees
- Edit category details

### Reports Admin
- View membership statistics
- View payment statistics
- Export data to CSV

---

## 📁 Important Files

```
routes/web.php                    → All application routes
app/Livewire/                    → Interactive components
app/Http/Controllers/Admin/      → Admin controllers
app/Services/                    → Business logic
resources/views/                 → Blade templates
tests/                           → Automated tests
```

---

## 🧪 Testing & Code Quality

```bash
# Run tests
php artisan test

# Format code
./vendor/bin/pint
```

---

## 📚 Documentation

| Document | What's Inside |
|----------|---------------|
| [SETUP.md](SETUP.md) | Step-by-step installation guide |
| [ADMIN_ROLES_GUIDE.md](ADMIN_ROLES_GUIDE.md) | How the admin system works |
| [ADMIN_QUICK_REFERENCE.md](ADMIN_QUICK_REFERENCE.md) | Quick commands and troubleshooting |
| [DEVELOPER_REFERENCE.md](DEVELOPER_REFERENCE.md) | Code examples for developers |
| [IMPLEMENTATION_CHECKLIST.md](IMPLEMENTATION_CHECKLIST.md) | What's built and what's planned |
| [KERNEL_CONFIGURATION.md](KERNEL_CONFIGURATION.md) | Middleware setup details |

---

## 💡 Need Help?

- Check the [SETUP.md](SETUP.md) guide for installation issues
- See [ADMIN_QUICK_REFERENCE.md](ADMIN_QUICK_REFERENCE.md) for common admin tasks
- Review application logs at `storage/logs/laravel.log`
- Visit [Laravel Documentation](https://laravel.com/docs) for framework help

---

## 📄 License

MIT License

