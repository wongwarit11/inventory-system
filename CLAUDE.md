# CLAUDE.md - Inventory System Project Guide

## Project Overview

- **Project Name:** ระบบสต็อกยาและเวชภัณฑ์ (Medicine and Medical Supplies Stock System)
- **Hospital:** โรงพยาบาลวัดห้วยปลากั้งเพื่อสังคม (Wat Huai Pla Kang Community Hospital)
- **Purpose:** Track medicine/medical supply inventory — receiving, issuing, adjustments, requisitions (ใบขอเบิก), and PDF purchase orders

**Tech Stack:**
| Layer | Technology |
|---|---|
| Framework | Laravel 12.x |
| PHP | 8.2+ |
| Database | MySQL (via XAMPP) |
| Frontend | Bootstrap 5.3, Font Awesome 6.0, Inter font |
| PDF | barryvdh/laravel-dompdf ^3.1 |
| Excel Export | maatwebsite/excel ^3.1 |
| Barcode | picqer/php-barcode-generator ^3.2 |
| Auth | Laravel session-based (custom username login) |

**Environments:**
- Local: XAMPP on Windows, `APP_URL=http://localhost:8000`, `php artisan serve`
- Production: Ubuntu server (deploy via git pull + artisan commands)

---

## Database

- **Database name:** `inventory_system`
- **Local credentials:** host=127.0.0.1, port=3306, user=root, password=(empty)
- **Session/Cache/Queue driver:** database

### Tables

| Table | Key Columns |
|---|---|
| `users` | id, username, password, fullname, email, phone, role (admin/manager/staff), status (active/inactive) |
| `departments` | id, name, description, status |
| `categories` | id, name, description, status |
| `suppliers` | id, name, contact_person, phone, email, address, status |
| `manufacturers` | id, name, contact_person, phone, email, address, status |
| `product_types` | id, name, description, status |
| `products` | id, product_code (unique), name, description, category_id, supplier_id, manufacturer_id, product_type_id, unit, cost_price, minimum_stock_level, image_path, status |
| `batches` | id, product_id, batch_number (unique), quantity, expiration_date, purchase_price, sale_price, received_date, supplier_id, location_id, notes, status |
| `stock_transactions` | id, product_id, batch_id (nullable), user_id, department_id (nullable), transaction_type (in/out/adjustment_in/adjustment_out/return_to_supplier), quantity, transaction_date, reference_doc, notes |
| `requisitions` | id, requisition_number (unique, format: REQ-YYYYMMDD-0001), user_id, department_id, requisition_date, status (pending/approved/issued/cancelled), notes, approved_by, approved_date, issued_by, issued_date |
| `requisition_items` | id, requisition_id, product_id, requested_quantity, issued_quantity, notes |
| `locations` | id, zone, shelf, slot, description, is_active |
| `sessions` | Laravel session storage |
| `cache` | Laravel cache storage |
| `jobs` | Laravel queue jobs |
| `assets` | (defined, not active in routes) |
| `borrows` | (defined, not active in routes) |
| `request_forms` | (defined, not active in routes) |

---

## Key Models & Relationships

### Product (`app/Models/Product.php`)
- `belongsTo` Category, Supplier, Manufacturer, ProductType
- `hasMany` Batch, StockTransaction, RequisitionItem
- Key: `minimum_stock_level` used for low-stock detection (compared against sum of batch quantities via raw SQL)

### Batch (`app/Models/Batch.php`)
- `belongsTo` Product, Location
- `hasMany` StockTransaction, RequisitionItem
- `quantity` is the live stock count — updated directly on every transaction

### StockTransaction (`app/Models/StockTransaction.php`)
- `belongsTo` Product, Batch, User, Department
- `transaction_type`: `in` (receive), `out` (issue/requisition), `adjustment_in`, `adjustment_out`

### Requisition (`app/Models/Requisition.php`)
- `belongsTo` User (requester), Department
- `hasMany` RequisitionItem (alias `items()`)
- Number format: `REQ-YYYYMMDD-0001`

### RequisitionItem (`app/Models/RequisitionItem.php`)
- `belongsTo` Requisition, Product
- Tracks both `requested_quantity` and `issued_quantity`

### User (`app/Models/User.php`)
- Roles: `admin`, `manager`, `staff`
- Login by `username` field (not `email`)

### Location (`app/Models/Location.php`)
- Accessor `fullLocation` returns `"{zone}-{shelf}-{slot}"`
- `is_active` boolean (cast automatically)

---

## Routes & Controllers

All routes under `auth` middleware except login/logout and the public API.

### Public API (no auth)
```
GET /api/products/{product}/batches   → returns batches JSON for AJAX dropdowns
```

### Auth Routes

| Route Group | Controller | Key Methods |
|---|---|---|
| `dashboard` | DashboardController | `index` — KPI stats, charts, low-stock table |
| `departments` (resource) | DepartmentController | CRUD |
| `categories` (resource) | CategoryController | CRUD |
| `suppliers` (resource) | SupplierController | CRUD |
| `manufacturers` (resource) | ManufacturerController | CRUD |
| `locations` (resource) | LocationController | CRUD |
| `product-types` (resource) | ProductTypeController | CRUD |
| `products` (resource) | ProductController | CRUD + `barcode` |
| `batches` (resource) | BatchController | CRUD + `barcode` |
| `stock-transactions` | StockTransactionController | `index`, `createReceive/storeReceive`, `createIssue/storeIssue`, `createAdjust/storeAdjust`, `editReceive/updateReceive` |
| `scanner` | ScannerController | `index`, `scan` |
| `requisitions` (resource) | RequisitionController | CRUD + `processRequisition`, `printPdf` |
| `users` (resource) | UserController | CRUD (admin only) |
| `reports/*` | ReportController | `stockReport`, `requisitionReport`, `lowStockProductsReport`, `exportLowStock`, `expiringBatchesReport`, `pendingRequisitionsReport`, `exportPurchaseOrder`, `exportPurchaseOrderSelected`, `purchaseOrderPreview`, `exportPurchaseOrderPreview` |

### Role-Based Access Control

| Feature | admin | manager | staff |
|---|---|---|---|
| Master Data (products, batches, etc.) | ✅ | ✅ | ❌ |
| Stock Transactions (receive/issue/adjust) | ✅ | ✅ | ❌ |
| View Stock Transaction List | ✅ | ✅ | ❌ |
| Create Requisition | ✅ | ✅ | ✅ (own only) |
| Process Requisition | ✅ | ✅ | ❌ |
| Reports | ✅ | ✅ (by URL) | ❌ |
| User Management | ✅ | ❌ | ❌ |
| Delete Requisition | ✅ | ❌ | ❌ |

---

## Features Implemented

- **Authentication:** Username/password login, inactive account check, CSRF protection
- **Dashboard:** KPI cards (products, batches, stock qty, low stock, expiring, pending requisitions, departments, suppliers, manufacturers, users), 7-day stock in/out chart, department consumption chart (filterable by 7/30/90/365/all days), low-stock table
- **Master Data CRUD:** Departments, Categories, Suppliers, Manufacturers, Locations, Product Types, Products (with image upload), Batches
- **Barcode Labels:** Code 128 barcode generation for Products (`/products/{id}/barcode`) and Batches (`/batches/{id}/barcode`)
- **Barcode Scanner:** `/scanner` — scan to look up product/batch
- **Stock Transactions:** Receive In, Issue Out, Stock Adjustment, Edit Receive (only `in` type editable)
- **Requisitions (ใบขอเบิก):** Create/Edit (pending only), process with batch selection (issues stock and creates transaction), PDF download, status flow: pending → approved/issued → cancelled
- **Reports:** Stock report (filterable by category/type/manufacturer), Requisition report (filterable by date/status/department), Low-stock products, Expiring batches (within 30 days), Pending requisitions
- **Purchase Order:** Preview with editable quantities, PDF export per supplier or selected products
- **Excel Export:** Low-stock products list via `app/Exports/LowStockExport.php`
- **PDF Export:** Requisition PDF and Purchase Order PDF using DomPDF + Thai font

---

## File Structure

```
app/
  Http/Controllers/
    AuthController.php
    DashboardController.php
    ProductController.php
    BatchController.php
    StockTransactionController.php
    RequisitionController.php
    ReportController.php
    UserController.php
    DepartmentController.php
    CategoryController.php
    SupplierController.php
    ManufacturerController.php
    LocationController.php
    ProductTypeController.php
    ScannerController.php
    Api/
      BatchApiController.php
      ProductApiController.php
    [*_old.php files are backups, not used in routes]
  Models/
    User.php, Product.php, Batch.php, StockTransaction.php
    Requisition.php, RequisitionItem.php
    Department.php, Category.php, Supplier.php, Manufacturer.php
    ProductType.php, Location.php
  Exports/
    LowStockExport.php
    StockReportExport.php

resources/views/
  layouts/app.blade.php         ← master layout (navbar, subnav, alerts)
  dashboard.blade.php
  auth/login.blade.php
  products/, batches/, categories/, departments/
  suppliers/, manufacturers/, locations/, product_types/
  stock_transactions/           ← receive.blade, issue.blade, adjust.blade, index.blade, edit_receive.blade
  requisitions/                 ← create.blade, edit.blade, show.blade, index.blade, pdf.blade
    partials/                   ← product_item_row.blade, product_item_edit_row.blade, batch_dropdown.blade
  reports/                      ← stock_report, requisition_report, low_stock_products_report, expiring_batches_report,
                                   pending_requisitions_report, purchase_order_pdf, purchase_order_preview
  barcodes/label.blade.php
  errors/404.blade.php, 500.blade.php

scripts/
  register_dompdf_fonts.php     ← run once to register THSarabunNew font for DomPDF

config/
  dompdf.php                    ← font_dir = storage_path('fonts/')

storage/fonts/                  ← THSarabunNew .ttf files + DomPDF .ufm/.json cache files
```

---

## Development Workflow

### Local Setup (XAMPP Windows)
```bash
# Start XAMPP Apache + MySQL, then:
php artisan serve          # runs on localhost:8000
php artisan migrate
php artisan storage:link   # for product images (storage/app/public → public/storage)
```

### Common Artisan Commands
```bash
php artisan migrate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan queue:work          # for queued jobs (queue_connection=database)
php artisan tinker
```

### Git Workflow
```
Local (XAMPP Windows) → commit → push to GitHub (main branch) → SSH to Ubuntu server → git pull → deploy
```

### Server Deploy Commands (Ubuntu)
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

---

## Important Notes

### Thai Font for DomPDF (PDF Generation)
- Font family: **THSarabunNew** — required for Thai text in PDFs
- Font `.ttf` files must be in `storage/fonts/`
- Run registration script once if `.ufm` cache files are missing or deleted:
  ```bash
  php scripts/register_dompdf_fonts.php
  ```
- DomPDF config `config/dompdf.php` sets `font_dir = storage_path('fonts/')`
- If PDF shows garbled Thai text → fonts not registered → run script above

### Stock Quantity Logic
- **`batches.quantity`** is the source of truth for live stock levels — it is updated directly on every `in`/`out`/`adjustment` transaction
- `products.current_stock` column exists in migration but is **not used** in application logic (all stock queries use `SUM(batches.quantity)`)
- Low-stock detection uses raw SQL subquery: `minimum_stock_level >= (SELECT COALESCE(SUM(batches.quantity), 0) FROM batches WHERE batches.product_id = products.id)`

### Requisition Number Format
- Auto-generated: `REQ-YYYYMMDD-0001` (sequential per day)

### Purchase Order Number Format
- Auto-generated: `PO-YYYYMMDD-XXXX`

### Image Upload
- Product images stored via `Storage::disk('public')` under `products/` subdirectory
- Stored path in DB: relative path without `public/` prefix (e.g., `products/abc.jpg`)
- Requires `php artisan storage:link` to create `public/storage` symlink

### Old/Backup Files
- Controllers ending in `_old.php` or `Old.php` (e.g., `BatchController_old.php`) are not registered in routes — kept for reference only
- Views ending in `.blade_old.php` or with date suffixes (e.g., `.blade2252569.php`) are backup copies

### Scanner
- `ScannerController` handles barcode scan lookup at `/scanner`
- Uses AJAX to `/api/products/{product}/batches` for batch dropdown (no auth required on this route)

---

## UI/Design

### Color Scheme
| Element | Color |
|---|---|
| Top navbar background | `#185FA5` |
| Sub navbar background | `#0C447C` |
| Sub navbar active indicator | `#85B7EB` |
| Body background | `#f0f4f8` |
| Card border | `#D3D1C7` |
| Alert success bg | `#EAF3DE` |
| Alert error bg | `#FCEBEB` |
| Link/active color | `#185FA5` |
| Avatar bg | `#B5D4F4` |
| Avatar text | `#0C447C` |

### Layout Structure
- **Top Navbar** (fixed, height 70px): Hospital logo icon + system title + sub-title, user dropdown (role display, logout)
- **Sub Navbar** (fixed, height 42px, top: 70px): Dashboard, ข้อมูลหลัก (dropdown), การจัดการสต็อก (dropdown), จัดการใบขอเบิก (dropdown), รายงาน (admin only), ตั้งค่าระบบ (admin only)
- **Main Content** (margin-top: 112px): `container-fluid`, flash alerts at top
- **Mobile**: Sub navbar hidden, hamburger button opens slide-in sidebar overlay

### CSS Conventions
- All custom CSS is embedded in `resources/views/layouts/app.blade.php` inside `<style>` tags (no separate CSS file)
- Per-page CSS goes in `@stack('styles')` / `@push('styles', ...)` blocks
- Bootstrap 5.3 loaded via CDN
- Font Awesome 6.0 loaded via CDN
- Cards use `border-radius: 12px; border: 0.5px solid #D3D1C7;`
- Buttons/inputs use `.rounded-pill` for pill shape where applicable
- Dropdowns in sub-navbar use fixed positioning (computed via `getBoundingClientRect()`) to avoid z-index clipping
