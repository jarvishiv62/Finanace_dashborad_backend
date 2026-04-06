# 💰 Finance Backend API

### _Because someone has to keep track of the money..._

<div align="center">

[![Laravel](https://img.shields.io/badge/Laravel-11-red?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![SQLite](https://img.shields.io/badge/SQLite-3-003B57?style=for-the-badge&logo=sqlite)](https://sqlite.org)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker)](https://docker.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

</div>

---

## 📖 The Story

> 📊 **Picture this:** A finance team is growing. The analyst needs to pull monthly trends. The manager wants a dashboard. The intern accidentally deleted a record — again. And somehow, everyone has access to everything.

Sound familiar? 😅

That's the problem this project was built to solve.

This is a backend API for a **finance dashboard system** — one where the right people can see the right data, the wrong people hit a polite `403`, deleted records aren't actually gone forever, and every response looks exactly the same no matter which endpoint you hit.

🔒 **No chaos.** 🎯 **No guesswork.** Just clean, role-aware, audit-friendly financial data management.

---

## 🚀 Quick Start (5 mins max)

```bash
git clone https://github.com/jarvishiv62/Finanace_dashborad_backend.git
cd Finanace_dashborad_backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

**API is now live at `http://localhost:8000`** 🎉

---

## 🔐 Default Credentials (Test these out!)

| Role       | Email                | Password    | Vibe            |
| ---------- | -------------------- | ----------- | --------------- |
| 🛡️ Admin   | admin@finance.test   | passadmin   | The boss 👑     |
| 📊 Analyst | analyst@finance.test | passanalyst | Data wizard 🧙‍♂️  |
| 👁️ Viewer  | viewer@finance.test  | passviewer  | Just looking 👀 |

---

## 📋 API Endpoints (What you can do)

### 🔍 API Reference Table

| Method | Endpoint                    | Auth Required | Roles Allowed          | Description             |
| ------ | --------------------------- | ------------- | ---------------------- | ----------------------- |
| POST   | `/api/auth/register`        | ❌            | All                    | Create new user account |
| POST   | `/api/auth/login`           | ❌            | All                    | Get access token        |
| POST   | `/api/auth/logout`          | ✅            | All                    | Revoke current token    |
| GET    | `/api/auth/me`              | ✅            | All                    | Get current user info   |
| GET    | `/api/records`              | ✅            | Viewer, Analyst, Admin | List financial records  |
| POST   | `/api/records`              | ✅            | Analyst, Admin         | Create new record       |
| GET    | `/api/records/{id}`         | ✅            | Viewer, Analyst, Admin | Get specific record     |
| PUT    | `/api/records/{id}`         | ✅            | Analyst (own), Admin   | Update record           |
| DELETE | `/api/records/{id}`         | ✅            | Admin                  | Soft delete record      |
| POST   | `/api/records/{id}/restore` | ✅            | Admin                  | Restore deleted record  |
| GET    | `/api/dashboard/summary`    | ✅            | Analyst, Admin         | Get financial summary   |
| GET    | `/api/dashboard/trends`     | ✅            | Analyst, Admin         | Get monthly trends      |
| GET    | `/api/dashboard/categories` | ✅            | Analyst, Admin         | Get category breakdown  |
| GET    | `/api/dashboard/recent`     | ✅            | Analyst, Admin         | Get recent activity     |
| GET    | `/api/users`                | ✅            | Admin                  | List all users          |
| POST   | `/api/users`                | ✅            | Admin                  | Create new user         |
| GET    | `/api/users/{id}`           | ✅            | Admin                  | Get user details        |
| PATCH  | `/api/users/{id}/role`      | ✅            | Admin                  | Update user role        |
| PATCH  | `/api/users/{id}/status`    | ✅            | Admin                  | Update user status      |
| GET    | `/api/health`               | ❌            | All                    | Health check endpoint   |

---

## 🛡️ Access Control (Who can do what)

| Action             | Viewer 👁️ | Analyst 📊 | Admin 👑 |
| ------------------ | --------- | ---------- | -------- |
| View Records       | ✅        | ✅         | ✅       |
| Create Records     | ❌        | ✅         | ✅       |
| Update Own Records | ❌        | ✅         | ✅       |
| Delete Records     | ❌        | ❌         | ✅       |
| View Dashboard     | ❌        | ✅         | ✅       |
| Manage Users       | ❌        | ❌         | ✅       |

---

## 🛠️ Tech Stack (The good stuff)

- **Backend**: Laravel 11 + PHP 8.2+ (modern af)
- **Database**: SQLite (production) / MySQL (local dev)
- **Authentication**: Laravel Sanctum (token-based 🔐)
- **Deployment**: Docker + Render (easy deploy)
- **Testing**: PHPUnit (because we're responsible)

---

## � Project Structure

```
finance-backend/
├── app/
│   ├── Enums/                          # Type-safe role and status values
│   │   ├── RoleEnum.php                # viewer | analyst | admin
│   │   └── StatusEnum.php             # active | inactive
│   │
│   ├── Helpers/
│   │   └── ApiResponse.php            # The one class every endpoint uses
│   │
│   ├── Http/
│   │   ├── Controllers/               # Thin — receive, delegate, respond
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── FinancialRecordController.php
│   │   │   └── UserController.php
│   │   │
│   │   ├── Middleware/                # Gates that run before controllers
│   │   │   ├── ActiveUserMiddleware.php    # Blocks inactive users with valid tokens
│   │   │   ├── RequestIdMiddleware.php     # Attaches X-Request-ID to every response
│   │   │   └── RoleMiddleware.php          # Enforces role access on route groups
│   │   │
│   │   ├── Requests/                  # All validation lives here, nowhere else
│   │   │   ├── FilterRecordsRequest.php
│   │   │   ├── LoginRequest.php
│   │   │   ├── RegisterRequest.php
│   │   │   ├── StoreFinancialRecordRequest.php
│   │   │   ├── StoreUserRequest.php
│   │   │   ├── UpdateFinancialRecordRequest.php
│   │   │   ├── UpdateUserRoleRequest.php
│   │   │   └── UpdateUserStatusRequest.php
│   │   │
│   │   └── Resources/                 # Controls exactly what goes out in responses
│   │       ├── FinancialRecordResource.php
│   │       └── UserResource.php       # Password never appears here — ever
│   │
│   ├── Models/
│   │   ├── FinancialRecord.php        # SoftDeletes + named query scopes
│   │   └── User.php                   # Enum casts, helper methods, relationships
│   │
│   ├── Policies/
│   │   └── FinancialRecordPolicy.php  # Record-level ownership logic
│   │
│   └── Services/                      # All business logic and DB queries live here
│       ├── DashboardService.php       # Aggregation queries — getSummary, getTrends...
│       └── FinancialRecordService.php # Filter, create, update, delete, restore
│
├── bootstrap/
│   └── app.php                        # Middleware registration + global exception handler
│
├── database/
│   ├── factories/
│   │   ├── FinancialRecordFactory.php # Used by tests to spin up records cleanly
│   │   └── UserFactory.php            # Supports .admin(), .analyst(), .inactive() states
│   │
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   └── 0001_01_01_000001_create_financial_records_table.php
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php         # Orchestrates the seeding order
│       ├── UserSeeder.php             # 4 users: admin, analyst, viewer, inactive
│       └── FinancialRecordSeeder.php  # 40 realistic records over 6 months
│
├── docker/
│   ├── nginx.conf                     # Nginx config for the container
│   └── supervisord.conf               # Runs nginx + php-fpm together
│
├── routes/
│   └── api.php                        # Route definitions only — zero logic
│
├── tests/
│   └── Feature/
│       ├── AccessControlTest.php      # The one that really matters — proves 403s work
│       ├── AuthTest.php
│       └── DashboardTest.php
│
├── .env.example                       # Safe to commit — no real secrets
├── .gitignore                         # .env is in here, always
├── Dockerfile                         # PHP 8.2 + nginx + supervisor
├── README.md                          # You are here
├── finance-api.postman_collection.json
└── render.yaml                        # Infrastructure as code for Render deployment
```

### 🤔 Why is it structured this way?

The folder structure follows a single rule: **logic lives where it belongs, not where it is convenient.**

- ✅ Validation belongs in `Requests/` — not sprinkled across controller methods
- ✅ Business logic belongs in `Services/` — not buried inside controllers
- ✅ Response shaping belongs in `Resources/` — not hardcoded arrays in each method
- ✅ Access rules belong in `Middleware/` and `Policies/` — not in `if` statements inside controllers

The result is that each file has one clear job. You can find anything in under ten seconds, and changing one thing does not break something three files away.

---

## �📦 Deployment (Go live!)

### Production (Render)

1. Push to GitHub
2. Connect repo to Render
3. Auto-deploys from `main` branch

**Live API**: https://finanace-dashborad-backend.onrender.com 🚀

### Environment Variables

| Variable                   | Default            | Description                             |
| -------------------------- | ------------------ | --------------------------------------- |
| `APP_NAME`                 | "Finance Backend"  | Application name                        |
| `APP_ENV`                  | `local`            | Environment (local/production)          |
| `APP_DEBUG`                | `true`             | Debug mode (false in production)        |
| `APP_KEY`                  | -                  | Laravel encryption key (auto-generated) |
| `APP_URL`                  | `http://localhost` | Application URL                         |
| `DB_CONNECTION`            | `mysql`            | Database driver (mysql/sqlite)          |
| `DB_HOST`                  | `127.0.0.1`        | Database host                           |
| `DB_PORT`                  | `3306`             | Database port                           |
| `DB_DATABASE`              | `finance_backend`  | Database name                           |
| `DB_USERNAME`              | `root`             | Database username                       |
| `DB_PASSWORD`              | -                  | Database password                       |
| `LOG_CHANNEL`              | `stack`            | Logging channel                         |
| `CACHE_DRIVER`             | `file`             | Cache driver                            |
| `SESSION_DRIVER`           | `file`             | Session driver                          |
| `QUEUE_CONNECTION`         | `sync`             | Queue connection                        |
| `SANCTUM_STATEFUL_DOMAINS` | `localhost`        | Sanctum stateful domains                |

### Production Environment Variables

For production deployment on Render:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=auto-generated
APP_URL=https://finanace-dashborad-backend.onrender.com
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
LOG_CHANNEL=stderr
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
SANCTUM_STATEFUL_DOMAINS=finanace-dashborad-backend.onrender.com
```

## 🧪 Testing (Make sure it works)

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --filter=AuthTest
php artisan test --filter=AccessControlTest
```

**31 tests total - 100% pass rate** ✅

---

## 📄 Postman Collection

Import `finance-api.postman_collection.json` for pre-configured requests:

- Auto-saves tokens to environment variables (magic ✨)
- Includes all endpoints with proper headers
- Ready for production and local testing

---

## 🛠️ Development Setup (Quick Start)

```bash
git clone https://github.com/jarvishiv62/Finanace_dashborad_backend.git
cd Finanace_dashborad_backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

**API is now live at `http://localhost:8000`** 🎉

---

## 🔧 Troubleshooting (When stuff breaks)

### Common Issues

**500 Error on Login** 😱

- Check database permissions: `chmod -R 777 database/`
- Verify APP_KEY is set: `php artisan key:generate`

**Authentication Not Working** 🤔

- Ensure Sanctum migrations ran: `php artisan migrate`
- Check User model has `HasApiTokens` trait

**Database Connection Failed** 💥

- Verify database file exists and is writable
- Check DB_CONNECTION in `.env` matches setup

### 🔍 Debug Tips

**Enable Debug Mode (Development Only)**

```env
APP_DEBUG=true
```

**Clear Cache**

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## � Future Improvements (What's next? 🔮)

**🚦 Rate Limiting** — Throttle auth endpoints to prevent brute-force attacks. Laravel's built-in `throttle` middleware makes this a ten-minute addition.

**⚡ Redis Caching** — Dashboard queries are read-heavy and change only when records are written. A 5-minute cache with invalidation on record mutations would reduce database load meaningfully at scale.

**📝 Audit Log Table** — An `audit_logs` table recording every mutation: who changed what, when, and what the previous value was. The first thing any compliance team would ask for.

**📄 CSV Export** — `GET /api/records/export?format=csv` with the same filter parameters as the records list. A common finance dashboard requirement.

**🔍 Full-Text Search** — The current search uses `LIKE %keyword%` which scans every row. For large datasets, a MySQL full-text index on `category` and `notes` with `MATCH AGAINST` is the right solution.

**🧪 Broader Test Coverage** — The current tests focus on access control and critical paths. Boundary conditions on validation rules, dashboard aggregation edge cases, and soft-delete restore flows would complete the suite.

**📱 Mobile API** — Add mobile-specific endpoints with optimized responses for mobile apps. Think smaller payloads, offline sync support, and push notification endpoints.

**🔄 Real-time Updates** — WebSocket integration for real-time dashboard updates. When someone adds a new record, everyone sees it instantly without refresh.

**🎨 Dark Mode Support** — User preference storage for dark/light mode themes. Because everyone deserves to vibe in their preferred aesthetic.

**📊 Advanced Analytics** — Machine learning-powered insights: spending patterns, anomaly detection, and predictive budgeting. Because why just track money when you can predict it?

---

## 📞 Support & Stuff

- **Live API**: https://finanace-dashborad-backend.onrender.com
- **Health Check**: https://finanace-dashborad-backend.onrender.com/api/health
- **Documentation**: This README (duh 📚)

---

<div align="center">

_Built with ❤️ using [Laravel 11](https://laravel.com) · [PHP 8.2](https://php.net) · [MySQL 8.0](https://mysql.com) · Deployed on [Render](https://render.com)_

</div>
