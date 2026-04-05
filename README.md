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

### 🔐 Authentication Stuff

- `POST /api/auth/register` - Create new user account
- `POST /api/auth/login` - Get your access token 🔑
- `POST /api/auth/logout` - Revoke that token
- `GET /api/auth/me` - Who am I? 👤

### 💳 Financial Records Management

- `GET /api/records` - List all records (paginated af)
- `POST /api/records` - Add new money move
- `GET /api/records/{id}` - Get specific record
- `PUT /api/records/{id}` - Update existing record
- `DELETE /api/records/{id}` - Soft delete (admin only 👑)
- `POST /api/records/{id}/restore` - Restore deleted (admin only)

### 📊 Dashboard Analytics (The cool stuff)

- `GET /api/dashboard/summary` - Income vs expenses totals
- `GET /api/dashboard/trends` - Monthly trends 📈
- `GET /api/dashboard/categories` - Where's the money going?
- `GET /api/dashboard/recent` - Latest activity feed

### 👥 User Management (Admins only)

- `GET /api/users` - List all users
- `POST /api/users` - Create new user
- `GET /api/users/{id}` - Get user details
- `PATCH /api/users/{id}/role` - Change their role
- `PATCH /api/users/{id}/status` - Activate/deactivate

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

## � Project Structure

```
finance-backend/
├── app/
│   ├── Enums/                    # Role and status enums
│   ├── Helpers/                  # API response helper
│   ├── Http/                     # Controllers, middleware, requests
│   ├── Models/                   # User, FinancialRecord models
│   ├── Policies/                 # Access control policies
│   └── Services/                 # Business logic
├── database/                     # Migrations, seeders, factories
├── docker/                       # Container configuration
├── routes/                       # API routes
├── tests/                        # Feature tests
└── config files                  # Laravel configuration
```

---

## ⚙️ How It Works

### 🔄 Request Lifecycle

Every request flows through: Request ID → Auth → Active User → Role Check → Controller → Service → Policy → Response

### 📊 Dashboard Queries

Single optimized SQL queries for performance:

- Summary: Aggregated income/expenses totals
- Trends: Monthly data with GROUP BY
- Categories: Spending breakdown by type

### 🗑️ Soft Deletes

Records are never permanently deleted - just marked with `deleted_at` timestamp.

---

## 📋 Access Control Matrix (The full breakdown)

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

## 📦 Deployment (Go live!)

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

## 🚦 Rate Limiting & Security

### Rate Limiting

The API implements rate limiting to prevent abuse:

- **Authentication endpoints**: 5 requests per minute
- **General API endpoints**: 60 requests per minute
- **Admin endpoints**: 30 requests per minute

Rate limiting headers are included in responses:

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1640995200
```

### CORS Configuration

CORS is configured for cross-origin requests:

**Allowed Origins:**

- `http://localhost:3000` (React development)
- `http://localhost:8080` (Vue development)
- `https://your-frontend-domain.com` (production)

**Allowed Methods:**

- `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`

**Allowed Headers:**

- `Content-Type`, `Accept`, `Authorization`, `X-Requested-With`

**Max Age:** 86400 seconds (24 hours)

### Security Headers

The API includes security headers for enhanced protection:

```http
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000; includeSubDomains
```

---

## 📞 Support & Stuff

- **Live API**: https://finanace-dashborad-backend.onrender.com
- **Health Check**: https://finanace-dashborad-backend.onrender.com/api/health
- **Documentation**: This README (duh 📚)

---

<div align="center">

_Built with ❤️ using [Laravel 11](https://laravel.com) · [PHP 8.2](https://php.net) · [MySQL 8.0](https://mysql.com) · Deployed on [Render](https://render.com)_

</div>
