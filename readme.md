# WorkInvoice

A comprehensive Laravel-based business management system for managing quotations, orders, invoices, payments, projects, inventory, and production workflows.

**Version**: 1.0.0 | **PHP**: 8.2+ | **Laravel**: 12.x | **License**: MIT

---

## Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [System Architecture](#system-architecture)
- [Core Features](#core-features)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
- [Database Schema](#database-schema)
- [API Routes](#api-routes)
- [Development Workflow](#development-workflow)
- [Configuration](#configuration)
- [Known Issues & Limitations](#known-issues--limitations)
- [Performance Considerations](#performance-considerations)
- [Contributing](#contributing)

---

## Overview

**WorkInvoice** is a full-stack business management platform designed to handle the complete lifecycle of:

- **Sales Operations**: From quotations through order fulfillment to invoicing and payment tracking
- **Supplier Management**: Purchase order tracking with stock receipt workflows
- **Project Management**: Project lifecycle tracking with expense management and advance payments
- **Inventory Control**: Product stock management with movement history and production workflows
- **Point of Sale**: Real-time sales transactions with daily summaries and receipt generation

The system is built with a modern Laravel backend (API + Web), Blade templating for views, Tailwind CSS + Alpine.js for the frontend, and DomPDF for document generation.

---

## Tech Stack

### Backend

- **Framework**: Laravel 12.x
- **PHP**: 8.2+ (type-safe, modern syntax)
- **ORM**: Eloquent with soft deletes and factory support
- **Authentication**: Laravel Breeze (session-based)
- **Database**: SQLite (default, configurable to MySQL/PostgreSQL)
- **Migrations**: Schema migrations with full version control

### Frontend

- **Templating**: Blade (Laravel's templating engine)
- **CSS Framework**: Tailwind CSS (utility-first styling)
- **JavaScript**: Alpine.js (reactive components without build step)
- **Build Tool**: Vite (fast module bundling and HMR)
- **Form Handling**: Blade components + HTML forms

### Dependencies

- **PDF Generation**: `barryvdh/laravel-dompdf` ^3.1
- **Testing**: PHPUnit, Mockery, PestPHP
- **Development**: Laravel Sail (Docker), Laravel Pail (logging), Laravel Pint (code formatting)
- **Utilities**: Laravel Tinker for REPL, Faker for seeding

---

## System Architecture

### Domain Model Hierarchy

```
Customer (central entity)
├── Quotation → QuotationItems
│   └── Order → OrderItems → Invoice → InvoiceItems → Payment
├── Order → OrderItems → Invoice
├── Invoice → InvoiceItems → Payment
├── Project → ProjectExpense (from converted order)
└── PurchaseOrder → PurchaseOrderItems → StockMovement

Product/Item (catalog)
├── Product (inventory-managed with stock)
│   └── StockMovement (audit trail)
└── Item (services/non-inventory items)

Sale (POS system)
├── SaleItem
└── StockMovement (if product)

Production
├── ProductionOrder → ProductionItems
└── (Link to Project via project_id, not fully implemented)
```

### Relationship Patterns

Most models use:

- **Soft deletes** (`SoftDeletes` trait) for non-destructive data removal
- **Timestamps** (`created_at`, `updated_at`) for audit trails
- **Factory support** (`HasFactory` trait) for testing/seeding
- **Status tracking** via `status` column (pending, approved, completed, etc.)
- **Financial precision** via `decimal` type casting (2 decimal places)

### Key Model Features

| Model             | Purpose                   | Key Methods                                                      |
| ----------------- | ------------------------- | ---------------------------------------------------------------- |
| `Customer`        | Client management         | `getTotalRevenueAttribute()`, `getDisplayNameAttribute()`        |
| `Quotation`       | Initial proposal          | `generateNumber()`, relations to orders/items                    |
| `Order`           | Customer order            | `generateNumber()`, conversion to invoice/project                |
| `Invoice`         | Billing document          | `generateNumber()`, payment tracking via `paid_amount`/`balance` |
| `Product`         | Inventory item            | Stock tracking, `StockMovement` audit                            |
| `Sale`            | Point-of-sale transaction | POS-specific fields, daily reporting                             |
| `Project`         | Project tracking          | Expense management, revenue calculation from orders              |
| `ProductionOrder` | Manufacturing order       | Status workflow (draft → started → completed)                    |
| `PurchaseOrder`   | Supplier order            | Stock receiving workflow, PDF generation                         |
| `Payment`         | Payment record            | Links invoice and customer, tracks payment method                |

---

## Core Features

### 1. Quotation-to-Invoice Workflow

- Create quotations with line items, discounts, and taxes
- Auto-generate unique quotation numbers (QT-xxxxx format)
- Duplicate quotations for rapid resale
- Convert quotations to orders with single action
- Link quotations to invoices for audit trail

### 2. Order Management

- Create orders from quotations or standalone
- Auto-numbered orders (ORD-xxxxx format)
- Adjustable line items with sort order
- Status tracking (pending, confirmed, shipped, delivered)
- Convert orders to invoices or projects
- PDF generation for printing/email

### 3. Invoice & Payment Tracking

- Auto-numbered invoices (INV-xxxxx)
- Sophisticated financial calculations (subtotal, discount, tax, grand total, balance)
- Multi-payment support with payment method tracking
- Payment records linked to specific invoices
- Status workflow (draft, sent, partial, paid, overdue)
- PDF invoices with DomPDF

### 4. Product & Inventory Management

- Products vs. Items distinction (inventory vs. services)
- Stock level tracking with low-stock alerts
- StockMovement audit trail (purchase, sale, adjustment)
- Stock adjustment interface
- Product search API (`/products/search`)

### 5. Purchase Order Management

- PO creation with supplier details
- Supplier item pricing and discounts
- Stock receipt workflow (receive goods from PO)
- Status tracking (draft, approved, received, invoiced)
- PDF generation for supplier communication
- Historical stock movement linking

### 6. Point of Sale (POS) System

- Real-time sale transactions with item search
- Tax calculation (additive, not percentage-based)
- Daily sales summary reports
- Receipt generation and printing
- Quick checkout workflow

### 7. Project Management

- Convert orders to projects for extended tracking
- Expense management (materials, labor, subcontracting)
- Advance payment tracking
- Project status workflow (active, completed, archived)
- Revenue calculation from linked orders
- ProjectExpense audit trail

### 8. Production Management

- Production order creation and tracking
- Status workflow (draft, started, completed)
- Production items (component-level tracking)
- Production item status workflow (pending, in-progress, completed)
- _Note: Project linking partially implemented (see Known Issues)_

### 9. User & Settings Management

- User authentication via Laravel Breeze
- Profile management
- Company settings (customizable via Settings model)
- Role-based access control framework (not fully implemented)

---

## Project Structure

### `/app/Models`

Core domain entities with Eloquent relationships, calculations, and business logic.

- **Pattern**: Each model includes relationships, accessors, and numeric type casting
- **Soft Deletes**: Most models use soft deletes for data integrity
- **Auto-numbering**: Quotation, Order, Invoice, PurchaseOrder generate unique numbers

### `/app/Http/Controllers`

Request handling and business logic orchestration.

- **Pattern**: Resource-based controllers (RESTful) with custom actions
- **Routes**: Defined in `/routes/web.php`
- **Naming**: Singular for unique resources (`ProfileController`), plural for collections (`CustomerController`)

Controllers include:

- `DashboardController` - Summary metrics and reporting
- `CustomerController` - Customer CRUD
- `QuotationController` - Quotation lifecycle + PDF
- `OrderController` - Order lifecycle + conversions
- `InvoiceController` - Invoice lifecycle + payments + PDF
- `ProductController` - Inventory + stock adjustment
- `SaleController` - POS transactions + daily summary
- `ProductionOrderController` - Production workflows
- `PurchaseOrderController` - Supplier orders + stock receiving
- `ProjectController` - Project management + expenses
- `PaymentController` - Payment processing
- `ReportController` - Business reports and analytics
- `SettingController` - Configuration management

### `/database/migrations`

Version-controlled schema definitions.

- **Timeline**: Foundation (users, cache) → Business entities (2024*01_01*_) → Recent features (2026*03*_)
- **Migration Order**: Pay attention to foreign key dependencies; migrations must run in sequence
- **Naming**: Descriptive action + timestamp, e.g., `2024_01_02_000001_add_discount_to_purchase_orders_table.php`

### `/resources/views`

Blade templates organizing the UI.

- **Layout**: Typically use `layouts.app` for authenticated pages
- **Partials**: Reusable form inputs, modals, components
- **PDFs**: Separate `pdf/` directory for document templates
- **Forms**: Create/edit forms use Blade component patterns

### `/routes`

- **`web.php`**: Main application routing (protected by auth middleware)
- **`auth.php`**: Authentication routes (Laravel Breeze)
- **`console.php`**: Artisan command registration

### `/config`

Application configuration files.

- **`app.php`**: Core app config (name, env, timezone, debug)
- **`database.php`**: Database connection settings
- **`auth.php`**: Authentication guards and password reset
- **`company.php`**: Custom company/business settings
- **`cache.php`, `queue.php`, `mail.php`, `session.php`**: Service configurations

### `/storage`

Runtime files (logs, cached views, uploads).

- **`/logs`**: Application logs (important for debugging)
- **`/framework`**: Laravel-generated cache

---

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ (for npm/vite)
- SQLite, MySQL, or PostgreSQL

### Installation

1. **Clone the repository**

    ```bash
    cd d:\Softwares\WorkInvoice
    ```

2. **Install dependencies** (automated via composer script)

    ```bash
    composer run setup
    ```

    This runs:
    - `composer install` - PHP dependencies
    - `.env` file generation (from `.env.example`)
    - `php artisan key:generate` - Application encryption key
    - `php artisan migrate --force` - Database tables
    - `npm install` - JavaScript dependencies
    - `npm run build` - Asset compilation

3. **Start development server** (all in one)

    ```bash
    composer run dev
    ```

    This concurrently runs:
    - `php artisan serve` - Laravel server (http://localhost:8000)
    - `php artisan queue:listen` - Job processing
    - `php artisan pail` - Log tailing
    - `npm run dev` - Vite dev server with HMR

4. **Access the application**
    - Navigate to `http://localhost:8000`
    - Default credentials: See `.env.example` or run `php artisan tinker` to create a user

### Running Tests

```bash
composer run test
```

Runs all tests in `/tests` with PHPUnit, includes error collection and analysis.

---

## Database Schema

### Core Tables

**users** - Authentication and user profiles

- `id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`
- Timestamps

**customers** - Client management

- `id`, `name`, `company_name`, `phone`, `email`, `address`, `notes`, `is_active`
- Timestamps, soft deletes

**items** - Service/non-inventory catalog

- `id`, `item_code`, `item_name`, `unit_price`, `description`, `is_active`
- Timestamps, soft deletes

**products** - Inventory items

- `id`, `product_code`, `product_name`, `unit_price`, `reorder_level`, `stock_level`, `description`, `is_active`
- Timestamps, soft deletes

**quotations** - Initial proposals

- `id`, `quotation_number`, `customer_id`, `quotation_date`, `expiry_date`
- Financial fields: `subtotal`, `discount`, `tax`, `grand_total`
- `status` (draft, sent, approved, rejected, converted)
- `notes`, `created_by`, timestamps, soft deletes

**quotation_items** - Quotation line items

- `id`, `quotation_id`, `item_id`, `quantity`, `unit_price`, `line_total`
- `description`, `sort_order`, timestamps

**orders** - Customer orders

- `id`, `order_number`, `customer_id`, `quotation_id`, `order_date`, `delivery_date`
- Financial fields: `subtotal`, `discount`, `tax`, `grand_total`
- `status`, `notes`, `delivery_info`, `created_by`
- Timestamps, soft deletes

**order_items** - Order line items

- `id`, `order_id`, `item_id`, `quantity`, `unit_price`, `line_total`
- `description`, `sort_order`, timestamps

**invoices** - Billing documents

- `id`, `invoice_number`, `customer_id`, `order_id`, `quotation_id`
- `date`, `due_date`
- Financial fields: `subtotal`, `discount`, `tax`, `grand_total`, `paid_amount`, `balance`
- `status` (draft, sent, partial, paid, overdue)
- `notes`, `terms`, `created_by`, timestamps, soft deletes

**invoice_items** - Invoice line items

- `id`, `invoice_id`, `item_id`, `quantity`, `unit_price`, `line_total`
- Timestamps

**payments** - Payment records

- `id`, `customer_id`, `invoice_id`, `amount`, `payment_date`, `payment_method`
- `reference`, `notes`, timestamps, soft deletes

**products** (inventory) - Physical inventory

- Stock tracking and movement audit trail

**stock_movements** - Inventory audit

- `id`, `product_id`, `movement_type` (purchase, sale, adjustment, production)
- `quantity`, `reference_id`, `notes`, timestamps

**purchase_orders** - Supplier orders

- `id`, `po_number`, `supplier_id`, `po_date`, `expected_delivery_date`
- Financial fields: `subtotal`, `discount`, `tax`, `grand_total`
- `status`, `notes`, timestamps, soft deletes

**purchase_order_items**

- `id`, `purchase_order_id`, `item_id`, `quantity`, `unit_cost`, `line_total`

**sales** - POS transactions

- `id`, `sale_number`, `sale_date`, `total_amount`
- `payment_method`, `notes`, timestamps

**sale_items** - Sale line items

- `id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `tax`, `line_total`

**projects** - Project tracking

- `id`, `project_name`, `order_id`, `status` (active, completed, archived)
- `start_date`, `end_date`, `total_revenue`, `advance_payment`
- Timestamps, soft deletes

**project_expenses** - Project costs

- `id`, `project_id`, `expense_date`, `description`, `amount`
- Timestamps

**production_orders** - Manufacturing orders

- `id`, `po_number`, `project_id`, `status` (draft, started, completed)
- `start_date`, `completion_date`, timestamps, soft deletes

**production_items** - Component tracking

- `id`, `production_order_id`, `item_id`, `quantity`, `status`
- Timestamps

**settings** - Configuration key-value store

- `id`, `key` (unique), `value` (JSON), timestamps

### Relationships Summary

```
Customer 1─────∞ Quotation
Customer 1─────∞ Order
Customer 1─────∞ Invoice
Customer 1─────∞ Payment

Quotation 1─────∞ QuotationItem
Quotation 1─────1 Order (conversion)

Order 1─────∞ OrderItem
Order 1─────1 Invoice (conversion)
Order 1─────1 Project (conversion)

Invoice 1─────∞ InvoiceItem
Invoice 1─────∞ Payment

Project 1─────∞ ProjectExpense
Project 1─────1 Order

Product 1─────∞ StockMovement
Product 1─────∞ SaleItem

PurchaseOrder 1─────∞ PurchaseOrderItem
PurchaseOrder 1─────∞ StockMovement (on receipt)

Production 1─────∞ ProductionItem
Production ∞─────? Project (foreign key present, not fully implemented)

Sale 1─────∞ SaleItem
```

---

## API Routes

All routes require authentication via `auth` middleware (Laravel Breeze session).

### Dashboard

- `GET /dashboard` - Summary metrics and KPIs

### Customers

- `GET/POST /customers` - List/create customers
- `GET/PATCH/DELETE /customers/{id}` - View/edit/delete

### Items (Services)

- `GET/POST /items` - List/create service items
- `GET /items/search` - AJAX search endpoint
- `PATCH/DELETE /items/{id}` - Edit/delete
- _Note: Show view not available_

### Products (Inventory)

- `GET/POST /products` - List/create products
- `GET /products/search` - AJAX search endpoint
- `GET /products/{id}/adjust-stock` - Stock adjustment form
- `PATCH /products/{id}/update-stock` - Update stock level
- `PATCH/DELETE /products/{id}` - Edit/delete

### Quotations

- `GET/POST /quotations` - List/create
- `GET /quotations/{id}` - View details
- `PATCH/DELETE /quotations/{id}` - Edit/delete
- `PATCH /quotations/{id}/status` - Update status
- `POST /quotations/{id}/duplicate` - Clone quotation
- `POST /quotations/{id}/convert-to-order` - Convert to order
- `GET /quotations/{id}/pdf` - Download PDF

### Orders

- `GET/POST /orders` - List/create
- `GET /orders/{id}` - View details
- `PATCH/DELETE /orders/{id}` - Edit/delete
- `PATCH /orders/{id}/status` - Update status
- `POST /orders/{id}/convert-to-invoice` - Create invoice
- `POST /orders/{id}/convert-to-project` - Create project

### Invoices

- `GET/POST /invoices` - List/create
- `GET /invoices/{id}` - View details
- `DELETE /invoices/{id}` - Delete
- `PATCH /invoices/{id}/status` - Update status
- `POST /invoices/{id}/payment` - Record payment
- `GET /invoices/{id}/pdf` - Download PDF
- _Note: Edit/update not available; re-create for changes_

### Purchase Orders

- `GET/POST /purchase-orders` - List/create
- `GET /purchase-orders/{id}` - View details
- `PATCH/DELETE /purchase-orders/{id}` - Edit/delete
- `PATCH /purchase-orders/{id}/status` - Update status
- `POST /purchase-orders/{id}/receive-stock` - Record stock receipt
- `GET /purchase-orders/{id}/pdf` - Download PDF

### Sales (POS)

- `GET /pos` - POS interface
- `POST /pos/checkout` - Complete sale transaction
- `GET /sales` - Sales history
- `GET /sales/{id}` - View sale details
- `GET /sales/{id}/receipt` - View/print receipt
- `DELETE /sales/{id}` - Void sale
- `GET /sales/daily-summary` - Daily report

### Projects

- `GET /projects` - List projects (with expenses)
- `GET /projects/{id}` - View project details
- `PATCH /projects/{id}/status` - Update status
- `POST /projects/{id}/expenses` - Add expense
- `DELETE /projects/{id}/expenses/{expense}` - Remove expense
- `PATCH /projects/{id}/advance` - Update advance payment

### Production Orders

- `GET/POST /production-orders` - List/create
- `GET /production-orders/{id}` - View details
- `PATCH/DELETE /production-orders/{id}` - Edit/delete
- `PATCH /production-orders/{id}/status` - Update status
- `POST /production-orders/{id}/start` - Mark as started
- `POST /production-orders/{id}/complete` - Mark as completed

### Reports

- `GET /reports` - Business reports dashboard

### Settings

- `GET /settings` - Edit company settings
- `PATCH /settings` - Update settings

### Authentication (Breeze)

- `GET /login`, `POST /login` - Login
- `POST /logout` - Logout
- `GET /register`, `POST /register` - Registration
- `GET /forgot-password`, `POST /forgot-password` - Password reset

### Debug/Test Routes

- `GET /test/delete-status` - Test deletion state
- `POST /test/destroy-customer/{id}` - Test destroy
- `GET /test/delete-model` - Test model deletion
- _⚠️ WARNING: These expose unprotected deletion endpoints_

---

## Development Workflow

### Code Organization by Feature

Create features in this order:

1. **Model** (`app/Models/FeatureName.php`) - Define relationships, casting, accessors
2. **Migration** (`database/migrations/`) - Create tables with foreign keys
3. **Controller** (`app/Http/Controllers/FeatureNameController.php`) - Business logic
4. **Routes** (`routes/web.php`) - Add resource routes
5. **Views** (`resources/views/features/`) - Blade templates

### Artisan Commands

```bash
# Create model with migration and factory
php artisan make:model Feature -mf

# Run migrations
php artisan migrate

# Roll back last migration batch
php artisan migrate:rollback

# Create controller
php artisan make:controller FeatureController --resource

# Open REPL
php artisan tinker

# Run tests
php artisan test

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Publishing vendor assets
php artisan vendor:publish
```

### Development Server

Start full dev environment with HMR:

```bash
composer run dev
```

This runs:

- Laravel server on port 8000
- Vite on port 5173 (with HMR proxy)
- Queue listener in background
- Log tailing for debugging

### Code Style

Uses **Laravel Pint** (PHP code formatter):

```bash
./vendor/bin/pint
```

Enforces PSR-12 standards with Laravel-specific rules.

### Testing

Test structure:

- **Unit tests**: `/tests/Unit/` - Isolated business logic
- **Feature tests**: `/tests/Feature/` - Full workflow testing

Run tests:

```bash
composer run test
```

Example test pattern:

```php
public function test_can_create_invoice(): void
{
    $customer = Customer::factory()->create();
    $response = $this->post('/invoices', [...]);
    $response->assertRedirect();
    $this->assertDatabaseHas('invoices', ...);
}
```

---

## Configuration

### Environment Variables (`.env`)

Critical variables:

```env
APP_NAME=WorkInvoice
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

MAIL_DRIVER=log
QUEUE_CONNECTION=database

SESSION_DRIVER=cookie
CACHE_DRIVER=file
```

### Database Selection

**SQLite** (default):

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

**MySQL**:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=workinvoice
DB_USERNAME=root
DB_PASSWORD=secret
```

### Company Settings

Customizable via `settings` table. Access in code:

```php
$companyName = Setting::getValue('company_name');
Setting::setValue('company_name', 'My Company');
```

---

## Known Issues & Limitations

### Critical Issues (Security/Data Integrity)

1. **Unprotected Debug Routes** ⚠️ HIGH PRIORITY
    - Routes `/test/delete-status`, `/test/destroy-customer/{id}`, `/test/delete-model` are **public** (outside auth middleware)
    - **Action**: Remove from `routes/web.php` or wrap in `auth` middleware before production

2. **Migration Rollback Bug**
    - File: `2026_03_25_000001_add_project_id_to_production_orders_table.php`
    - Issue: Uses `dropForeignKey()` instead of `dropForeign()` in `down()`
    - **Action**: Correct foreign key drop syntax

### Business Logic Issues

3. **Tax Calculation Inconsistency**
    - **Quotation/Order/Invoice**: Tax calculated as **percentage** on subtotal
    - **Sales (POS)**: Tax as **absolute amount** (additive)
    - **Purchase Orders**: Tax calculated on (subtotal - discount)
    - **Impact**: Mixed reporting; risk of incorrect totals
    - **Recommendation**: Standardize to percentage-based calculation

4. **Quotation-to-Order Conversion Not Idempotent**
    - Converting same quotation multiple times creates duplicate orders
    - **Action**: Add guard in `QuotationController@convertToOrder` or implement once-only state

5. **ProductionOrder-Project Linkage Incomplete**
    - Schema includes `project_id` foreign key
    - UI forms don't accept or persist `project_id`
    - **Impact**: Cannot link production to projects end-to-end

### Performance Issues

6. **N+1 Query Risk: Profit Calculation** ⚠️ MEDIUM PRIORITY
    - `Project::profit` accessor queries `expenses()->sum()` per project
    - Dashboard sums profits over collections (triggers N+1)
    - **Location**: `app/Models/Project.php`, `DashboardController.php`
    - **Fix**: Use eager load with `with(['expenses'])` + collection sum

7. **Daily Project Income Calculation**
    - Uses `created_at` date, not payment date
    - Includes unbooked revenue (orders not yet invoiced/paid)
    - **Impact**: Daily income overstated
    - **Recommendation**: Filter by `invoices.status = 'paid'` with `payments.payment_date`

### Feature Gaps

8. **Invoice Edit Disabled**
    - No `/invoices/{id}/edit` or `PATCH` endpoint
    - Invoices are immutable once created
    - **Workaround**: Delete and recreate (if soft-delete allowed)

9. **Role-Based Access Control**
    - Authentication framework exists (Breeze)
    - Authorization checks (`Gate`, `Policy`) not implemented
    - All authenticated users have full access

10. **Item vs. Product Distinction**
    - Business rules unclear (when to use item vs. product?)
    - Duplication of master data management
    - **Consideration**: Refactor to unified catalog with flags

---

## Performance Considerations

### Query Optimization

**Best Practices:**

- Always eager-load relationships to avoid N+1
    ```php
    Order::with(['customer', 'items', 'invoice'])->get();
    ```
- Use `select()` to limit columns when querying lists
    ```php
    Customer::select('id', 'name', 'email')->get();
    ```
- Use database computed columns for constants (`grand_total` calculations)

**Problematic Patterns in Current Code:**

- Dashboard loops over projects and calculates `->profit` (N query per project)
- Sale checkout queries products per item

### Caching Strategy

Current gaps:

- No query caching for settings
- Quotation/Order totals recalculated on each view
- No database result caching

**Recommendations:**

```php
// Cache company settings
$name = Cache::remember('company_name', now()->addDay(), function() {
    return Settings::getValue('company_name');
});

// Cache financial summaries
$total = Cache::remember(
    'customer:' . $id . ':total_revenue',
    now()->addDay(),
    fn() => Customer::find($id)->getTotalRevenueAttribute()
);
```

### Database Indexing

Missing indexes (recommendations):

```sql
-- High-traffic columns
CREATE INDEX idx_invoices_customer_id ON invoices(customer_id);
CREATE INDEX idx_invoices_status ON invoices(status);
CREATE INDEX idx_stock_movements_product_id ON stock_movements(product_id);

-- Date range queries
CREATE INDEX idx_invoices_created_at ON invoices(created_at);
CREATE INDEX idx_sales_sale_date ON sales(sale_date);
```

---

## Contributing

### Code Standards

1. **Style**: Follow Laravel Pint rules (`./vendor/bin/pint`)
2. **Type Safety**: Use PHP 8.2+ type hints on all public methods
3. **Documentation**: PHPDoc comments on models and public methods
4. **Testing**: Write tests for new features (unit + feature)

### Branching Strategy

- `main` - Production-ready code
- `develop` - Integration branch
- `feature/description` - Feature branches
- `bugfix/description` - Bug fixes

### Pull Request Process

1. Create feature branch from `develop`
2. Make changes with passing tests
3. Run `composer run test` and `./vendor/bin/pint`
4. Submit PR with description of changes
5. Code review required before merge
6. Merge to `develop`, test in staging, release to `main`

---

## Support & Documentation

- **Laravel Docs**: https://laravel.com/docs/12
- **Blade Templating**: https://laravel.com/docs/blade
- **Eloquent ORM**: https://laravel.com/docs/eloquent
- **Vite**: https://vitejs.dev
- **Tailwind CSS**: https://tailwindcss.com/docs
- **DomPDF**: https://github.com/barryvdh/laravel-dompdf

---

**Last Updated**: March 26, 2026  
**Maintainer**: Development Team  
**Status**: Production-Ready (with known issues noted above)
