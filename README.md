# Backoffice ERP

A modern Laravel-based ERP system for managing **Transfers**, **Hotels**, **Tours**, **Bookings**, and **Agents**. The application supports role-based access with separate dashboards and permissions for **Super Admin** and **Agent** users.

> **Project Status:** 🚧 Under Active Development

---

## Features

### Authentication
- Laravel Breeze authentication
- Secure login/logout
- Password hashing
- Remember Me support

### Authorization
- Role-based access control using Spatie Laravel Permission
- Super Admin
- Agent

### Dashboard
- Separate dashboards for each role
- Dashboard statistics
- Responsive layout
- Shared dashboard template

### Agent Management *(In Progress)*
- List agents
- Search agents
- Create agent
- Edit agent
- Activate/Deactivate agent
- Pagination

### Upcoming Modules
- Hotels
- Transfers
- Transfer Rates
- Tours
- Tour Requests
- Bookings
- Reports
- Settings

---

## Tech Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Laravel Breeze
- Spatie Laravel Permission
- Blade
- Tailwind CSS
- Vite

---

## Installation

Clone the repository

```bash
git clone <repository-url>
cd backoffice-erp
```

Install dependencies

```bash
composer install
npm install
```

Create environment file

```bash
cp .env.example .env
```

Generate application key

```bash
php artisan key:generate
```

Configure your database in the `.env` file.

Run migrations

```bash
php artisan migrate
```

Seed the database

```bash
php artisan db:seed
```

Compile frontend assets

```bash
npm run dev
```

Start the development server

```bash
php artisan serve
```

---

## Default Login

### Super Admin

```
Email: admin@example.com
Password: password
```

> Change the default credentials immediately after the first login.

---

## Project Structure

```
app
├── Http
│   ├── Controllers
│   │   ├── Admin
│   │   └── Agent
│   ├── Middleware
│   └── Requests
│
├── Models
│
├── Repositories
│
├── Services
│
└── View

resources
├── views
│   ├── admin
│   ├── agent
│   ├── components
│   └── layouts

routes
├── web.php
```

---

## Development Architecture

The project follows a layered architecture:

```
Controller
      │
      ▼
Service
      │
      ▼
Repository
      │
      ▼
Eloquent Model
```

### Responsibilities

**Controllers**
- Handle HTTP requests
- Validate input
- Return views/responses

**Services**
- Business logic
- Transactions
- Domain rules

**Repositories**
- Database queries
- Data retrieval
- Query optimization

---

## Roles

### Super Admin

- Dashboard
- Manage Agents
- Manage Hotels
- Manage Transfers
- Manage Tours
- Manage Bookings
- Manage Transfer Rates
- Manage Tour Requests

### Agent

- Dashboard
- View Hotels
- View Transfers
- Create Bookings
- View Own Bookings

---

## Coding Standards

- PSR-12 Coding Standard
- Laravel Best Practices
- Resource Controllers
- Form Request Validation
- Service Layer
- Repository Pattern
- Reusable Blade Components

---

## Git Workflow

Feature branches are recommended.

```
main
 ├── develop
 │     ├── feature/agent-management
 │     ├── feature/hotels
 │     ├── feature/transfers
 │     └── feature/bookings
```

---

## Roadmap

- [x] Laravel Installation
- [x] Laravel Breeze Authentication
- [x] Role-based Authentication
- [x] Dashboard Layout
- [ ] Agent Management
- [ ] Hotel Module
- [ ] Transfer Module
- [ ] Booking Module
- [ ] Tour Module
- [ ] Reports
- [ ] Notifications
- [ ] Audit Logs

---

## License

This project is proprietary and intended for internal use unless otherwise specified.