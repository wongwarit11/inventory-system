<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome (สำหรับ Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
        }
        .navbar {
            background-color: #007bff; /* Primary color for Navbar */
            box-shadow: 0 2px 4px rgba(0,0,0,.08);
        }
        .navbar-brand, .nav-link {
            color: #ffffff !important;
        }
        .navbar-brand:hover, .nav-link:hover {
            color: #e2e6ea !important;
        }
        /* Sidebar styles for desktop */
        .sidebar-desktop {
            background-color: #343a40; /* Dark sidebar */
            color: #ffffff;
            min-height: 100vh;
            padding-top: 20px;
            position: fixed;
            width: 250px;
            top: 0;
            left: 0;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0,0,0,.1);
            z-index: 1020; /* Below fixed navbar */
        }
        .sidebar-desktop .nav-link {
            color: #adb5bd;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.2s ease-in-out;
        }
        .sidebar-desktop .nav-link:hover, .sidebar-desktop .nav-link.active {
            background-color: #007bff;
            color: #ffffff;
        }
        .sidebar-desktop .nav-link.active {
            font-weight: bold;
        }
        .sidebar-desktop .nav-link i {
            margin-right: 10px;
        }
        .sidebar-desktop .sidebar-heading {
            color: #ffffff !important;
            padding-left: 15px;
            padding-right: 15px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Main content styles for desktop */
        .main-content {
            margin-left: 250px; /* Adjust content to the right of sidebar */
            padding: 20px;
            padding-top: 110px !important; /* Ensure content is below fixed navbar */
        }

        /* Custom styles for alerts */
        .alert-custom {
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
        }
        .alert-custom .alert-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        .alert-custom.alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        .alert-custom.alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        .alert-custom .btn-close {
            margin-left: auto;
        }

        /* Responsive adjustments for forms/cards */
        .container-fluid > .container { /* Target nested containers for forms */
            max-width: 800px; /* Max width for better readability on large screens */
        }
        .card {
            border-radius: 1rem; /* Rounded card corners */
        }
        .form-control.rounded-pill, .form-select.rounded-pill {
            border-radius: 2rem !important; /* More rounded input and select fields */
        }
        .btn.rounded-pill {
            border-radius: 2rem !important;
        }
        /* Custom Tooltip Styles - GLOBAL */
        .custom-tooltip-icon {
            cursor: help;
            color: #6c757d; /* Grey color for icon */
        }
        /* Spinner for Loading State - GLOBAL */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }

        /* Media queries for responsiveness */
        @media (max-width: 767.98px) { /* For screens smaller than md (768px) */
            .sidebar-desktop {
                display: none; /* Hide desktop sidebar on mobile */
            }

            .main-content {
                margin-left: 0; /* No margin on mobile */
                padding-top: 70px !important; /* Adjust padding for mobile navbar height */
            }

            .navbar-toggler-offcanvas {
                display: block; /* Show toggler for offcanvas */
            }
            .navbar-brand {
                margin-left: 10px; /* Adjust brand position */
            }
        }
        @media (min-width: 768px) { /* For screens md (768px) and larger */
            .navbar-toggler-offcanvas {
                display: none; /* Hide offcanvas toggler on desktop */
            }
        }

        /* Offcanvas specific styles (Bootstrap handles most of it) */
        .offcanvas {
            background-color: #343a40; /* Match sidebar background */
            color: #ffffff;
        }
        .offcanvas .offcanvas-header {
            border-bottom: 1px solid rgba(255,255,255,.1);
            padding: 1rem 1.25rem;
        }
        .offcanvas .offcanvas-title {
            color: #ffffff;
            font-weight: bold;
        }
        .offcanvas .btn-close {
            filter: invert(1); /* Make close button white */
        }
        .offcanvas .nav-link {
            color: #adb5bd;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.2s ease-in-out;
        }
        .offcanvas .nav-link:hover, .offcanvas .nav-link.active {
            background-color: #007bff;
            color: #ffffff;
        }
        .offcanvas .nav-link.active {
            font-weight: bold;
        }
        .offcanvas .nav-link i {
            margin-right: 10px;
        }
        .offcanvas .sidebar-heading {
            color: #ffffff !important;
            padding-left: 15px;
            padding-right: 15px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
    @stack('styles') {{-- สำหรับ CSS เพิ่มเติมที่ต้องการจากแต่ละหน้า --}}
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark fixed-top">
            <div class="container-fluid">
                {{-- Toggler for Offcanvas Sidebar (visible on mobile) --}}
                <button class="navbar-toggler-offcanvas btn btn-link text-white me-2 d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar" aria-label="Toggle navigation">
                    <i class="fas fa-bars fa-lg"></i>
                </button>

                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <i class="fas fa-warehouse me-2"></i> {{ config('app.name', 'ระบบจัดการคลังสินค้า') }}
                </a>
                {{-- Default Navbar Toggler (can be hidden if offcanvas is primary mobile nav) --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        {{-- สามารถเพิ่มเมนูด้านซ้ายได้ที่นี่ --}}
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle me-2"></i> {{ Auth::user()->fullname ?? Auth::user()->username }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-id-badge me-2"></i> บทบาท: {{ ucfirst(Auth::user()->role) }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <div class="d-flex">
            {{-- Desktop Sidebar (visible on desktop, hidden on mobile) --}}
            <div class="sidebar-desktop d-none d-md-block">
                <h5 class="text-white text-center mb-4">เมนูหลัก</h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <h6 class="sidebar-heading">
                            <span>ข้อมูลหลัก</span>
                        </h6>
                    </li>
                    @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'manager'))
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                                <i class="fas fa-building"></i> แผนก (Departments)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                <i class="fas fa-tags"></i> หมวดหมู่ (Categories)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                                <i class="fas fa-truck"></i> ผู้จัดจำหน่าย (Suppliers)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('manufacturers.*') ? 'active' : '' }}" href="{{ route('manufacturers.index') }}">
                                <i class="fas fa-industry"></i> ผู้ผลิต (Manufacturers)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('product-types.*') ? 'active' : '' }}" href="{{ route('product-types.index') }}">
                                <i class="fas fa-boxes"></i> ประเภทสินค้า (Product Types)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                <i class="fas fa-box"></i> สินค้า (Products)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('batches.*') ? 'active' : '' }}" href="{{ route('batches.index') }}">
                                <i class="fas fa-boxes"></i> ล็อตสินค้า (Batches)
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <h6 class="sidebar-heading">
                            <span>การจัดการสต็อก</span>
                        </h6>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('stock_transactions.index') ? 'active' : '' }}" href="{{ route('stock_transactions.index') }}">
                            <i class="fas fa-history"></i> รายการสต็อก
                        </a>
                    </li>
                    @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'manager'))
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('stock_transactions.receive.create') ? 'active' : '' }}" href="{{ route('stock_transactions.receive.create') }}">
                                <i class="fas fa-arrow-alt-circle-down"></i> รับเข้าสินค้า
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('stock_transactions.issue.create') ? 'active' : '' }}" href="{{ route('stock_transactions.issue.create') }}">
                                <i class="fas fa-arrow-alt-circle-up"></i> จ่ายออกสินค้า
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('stock_transactions.adjust.create') ? 'active' : '' }}" href="{{ route('stock_transactions.adjust.create') }}">
                                <i class="fas fa-sliders-h"></i> ปรับปรุงสต็อก
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <h6 class="sidebar-heading">
                            <span>การจัดการใบขอเบิก</span>
                        </h6>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('requisitions.*') ? 'active' : '' }}" href="{{ route('requisitions.index') }}">
                            <i class="fas fa-file-invoice"></i> รายการใบขอเบิก
                        </a>
                    </li>
                    @if (Auth::check() && Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <h6 class="sidebar-heading">
                                <span>รายงาน</span>
                            </h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('reports.stock') ? 'active' : '' }}" href="{{ route('reports.stock') }}">
                                <i class="fas fa-chart-pie"></i> รายงานสต็อก
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('reports.requisition') ? 'active' : '' }}" href="{{ route('reports.requisition') }}">
                                <i class="fas fa-clipboard-list"></i> รายงานการเบิก
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('reports.low_stock_products') ? 'active' : '' }}" href="{{ route('reports.low_stock_products') }}">
                                <i class="fas fa-exclamation-triangle"></i> สินค้าสต็อกต่ำ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('reports.expiring_batches') ? 'active' : '' }}" href="{{ route('reports.expiring_batches') }}">
                                <i class="fas fa-calendar-times"></i> ล็อตใกล้หมดอายุ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('reports.pending_requisitions') ? 'active' : '' }}" href="{{ route('reports.pending_requisitions') }}">
                                <i class="fas fa-hourglass-half"></i> ใบขอเบิกค้างอนุมัติ
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <h6 class="sidebar-heading">
                            <span>ตั้งค่าระบบ</span>
                        </h6>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <i class="fas fa-users-cog"></i> จัดการผู้ใช้งาน
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Offcanvas Sidebar (visible on mobile, hidden on desktop) --}}
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasSidebarLabel">เมนูหลัก</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <h6 class="sidebar-heading">
                                <span>ข้อมูลหลัก</span>
                            </h6>
                        </li>
                        @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'manager'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                                    <i class="fas fa-building"></i> แผนก (Departments)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                    <i class="fas fa-tags"></i> หมวดหมู่ (Categories)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                                    <i class="fas fa-truck"></i> ผู้จัดจำหน่าย (Suppliers)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('manufacturers.*') ? 'active' : '' }}" href="{{ route('manufacturers.index') }}">
                                    <i class="fas fa-industry"></i> ผู้ผลิต (Manufacturers)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('product-types.*') ? 'active' : '' }}" href="{{ route('product-types.index') }}">
                                    <i class="fas fa-boxes"></i> ประเภทสินค้า (Product Types)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                    <i class="fas fa-box"></i> สินค้า (Products)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('batches.*') ? 'active' : '' }}" href="{{ route('batches.index') }}">
                                    <i class="fas fa-boxes"></i> ล็อตสินค้า (Batches)
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <h6 class="sidebar-heading">
                                <span>การจัดการสต็อก</span>
                            </h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('stock_transactions.index') ? 'active' : '' }}" href="{{ route('stock_transactions.index') }}">
                                <i class="fas fa-history"></i> รายการสต็อก
                            </a>
                        </li>
                        @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'manager'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('stock_transactions.receive.create') ? 'active' : '' }}" href="{{ route('stock_transactions.receive.create') }}">
                                    <i class="fas fa-arrow-alt-circle-down"></i> รับเข้าสินค้า
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('stock_transactions.issue.create') ? 'active' : '' }}" href="{{ route('stock_transactions.issue.create') }}">
                                    <i class="fas fa-arrow-alt-circle-up"></i> จ่ายออกสินค้า
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('stock_transactions.adjust.create') ? 'active' : '' }}" href="{{ route('stock_transactions.adjust.create') }}">
                                    <i class="fas fa-sliders-h"></i> ปรับปรุงสต็อก
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <h6 class="sidebar-heading">
                                <span>การจัดการใบขอเบิก</span>
                            </h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('requisitions.*') ? 'active' : '' }}" href="{{ route('requisitions.index') }}">
                                <i class="fas fa-file-invoice"></i> รายการใบขอเบิก
                            </a>
                        </li>
                        @if (Auth::check() && Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <h6 class="sidebar-heading">
                                    <span>รายงาน</span>
                                </h6>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('reports.stock') ? 'active' : '' }}" href="{{ route('reports.stock') }}">
                                    <i class="fas fa-chart-pie"></i> รายงานสต็อก
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('reports.requisition') ? 'active' : '' }}" href="{{ route('reports.requisition') }}">
                                    <i class="fas fa-clipboard-list"></i> รายงานการเบิก
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('reports.low_stock_products') ? 'active' : '' }}" href="{{ route('reports.low_stock_products') }}">
                                    <i class="fas fa-exclamation-triangle"></i> สินค้าสต็อกต่ำ
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('reports.expiring_batches') ? 'active' : '' }}" href="{{ route('reports.expiring_batches') }}">
                                    <i class="fas fa-calendar-times"></i> ล็อตใกล้หมดอายุ
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('reports.pending_requisitions') ? 'active' : '' }}" href="{{ route('reports.pending_requisitions') }}">
                                    <i class="fas fa-hourglass-half"></i> ใบขอเบิกค้างอนุมัติ
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <h6 class="sidebar-heading">
                                <span>ตั้งค่าระบบ</span>
                            </h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas fa-users-cog"></i> จัดการผู้ใช้งาน
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <main class="py-4 main-content">
                {{-- ส่วนสำหรับแสดงข้อความแจ้งเตือน Success/Error --}}
                <div class="container">
                    @if (session('success'))
                        <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle alert-icon"></i>
                            <div>{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                            <i class="fas fa-times-circle alert-icon"></i>
                            <div>{{ session('error') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap Tooltips globally
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // You can add more global JavaScript here if needed
        });
    </script>
    @stack('scripts') {{-- สำหรับ JavaScript เพิ่มเติมที่ต้องการจากแต่ละหน้า --}}
</body>
</html>
