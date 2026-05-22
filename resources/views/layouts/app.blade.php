<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - ระบบสต็อกยาและเวชภัณฑ์</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: #f0f4f8; margin: 0; padding: 0; }

        /* ===== TOP NAVBAR ===== */
        .nav-top {
            background: #185FA5;
            height: 70px;  /* เพิ่มจาก 50px */
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1050;
        }
        .nav-brand {
            color: white;
            font-size: 18px;  /* เพิ่มจาก 15px */
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            letter-spacing: 0.01em;
        }
        .nav-brand:hover { color: #B5D4F4; }
        .nav-brand-icon {
            width: 44px; height: 44px;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .nav-brand-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }
        .nav-brand-title {
            font-size: 24px;
            font-weight: 600;
            color: white;
            line-height: 1.2;
        }
        .nav-brand-sub {
            font-size: 12px;
            color: rgba(255,255,255,0.65);
            font-weight: 400;
            letter-spacing: 0.03em;
        }
        .nav-right { display: flex; align-items: center; gap: 10px; }
        .search-btn {
            background: rgba(255,255,255,0.15);
            border: 0.5px solid rgba(255,255,255,0.25);
            border-radius: 8px;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            color: rgba(255,255,255,0.8);
            font-size: 12px;
        }
        .search-btn:hover { background: rgba(255,255,255,0.25); }
        .user-dropdown { position: relative; }
        .user-btn {
            background: rgba(255,255,255,0.15);
            border: 0.5px solid rgba(255,255,255,0.25);
            border-radius: 8px;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            color: white;
            font-size: 12px;
            font-weight: 500;
        }
        .user-btn:hover { background: rgba(255,255,255,0.25); }
        .avatar {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: #B5D4F4;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 500; color: #0C447C;
            flex-shrink: 0;
        }
        .dropdown-menu-custom {
            position: absolute;
            right: 0; top: calc(100% + 6px);
            background: white;
            border: 0.5px solid #D3D1C7;
            border-radius: 10px;
            min-width: 180px;
            overflow: hidden;
            z-index: 1060;
            display: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .dropdown-menu-custom.open { display: block; }
        .drop-item {
            padding: 10px 14px;
            font-size: 13px;
            color: #2C2C2A;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .drop-item:hover { background: #f0f4f8; color: #185FA5; }
        .drop-item.danger { color: #A32D2D; }
        .drop-item.danger:hover { background: #FCEBEB; }
        .drop-divider { border-top: 0.5px solid #E0DDD5; }
        .drop-role {
            padding: 8px 14px;
            font-size: 11px;
            color: #888780;
            background: #F7F5EF;
            border-bottom: 0.5px solid #E0DDD5;
        }

        /* ===== SUB NAVBAR ===== */
        .nav-sub {
            background: #0C447C;
            height: 42px;
            display: flex;
            align-items: stretch;
            padding: 0 20px;
            position: fixed;
            top: 70px; left: 0; right: 0;
            z-index: 1045;
            overflow-x: auto;
            overflow-y: visible;
        }
        .nav-sub::-webkit-scrollbar { display: none; }
        .nav-sub-item {
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            padding: 0 16px;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            white-space: nowrap;
            text-decoration: none;
            transition: all 0.15s;
        }
        .nav-sub-item:hover { color: white; background: rgba(255,255,255,0.07); }
        .nav-sub-item.active { color: white; border-bottom: 2px solid #85B7EB; font-weight: 500; }

        /* ===== DROPDOWN SUBMENU ===== */
        .nav-sub-item { position: relative; }
        .nav-sub-dropdown {
            position: fixed;
            background: white;
            border: 0.5px solid #D3D1C7;
            border-radius: 10px;
            min-width: 220px;
            z-index: 9999;
            display: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 6px 0;
        }
        .nav-sub-dropdown.show { display: block; }
        .nav-sub-dropdown a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            font-size: 13px;
            color: #2C2C2A;
            text-decoration: none;
        }
        .nav-sub-dropdown a:hover { background: #f0f4f8; color: #185FA5; }
        .nav-sub-dropdown a.active { color: #185FA5; font-weight: 500; background: #E6F1FB; }
        .nav-sub-dropdown .drop-section {
            font-size: 10px;
            color: #888780;
            padding: 8px 16px 4px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-top: 0.5px solid #E0DDD5;
            margin-top: 4px;
        }

        /* ===== FIX Z-INDEX ===== */
        .nav-top { z-index: 1050; }
        .nav-sub { z-index: 1040; overflow-y: visible !important; }
        .nav-sub-dropdown { z-index: 1039; }
        .main-content { z-index: 0; position: relative; }
        .card { position: relative; z-index: 0; }
        .table-responsive { position: relative; z-index: 0; }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-top: 112px;
            padding: 20px;
            min-height: calc(100vh - 92px);
            position:relative;
            z-index: 1;
        }

        /* ===== ALERTS ===== */
        .alert-custom {
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }
        .alert-custom.alert-success { background: #EAF3DE; color: #3B6D11; border: 0.5px solid #C0DD97; }
        .alert-custom.alert-danger { background: #FCEBEB; color: #A32D2D; border: 0.5px solid #F7C1C1; }

        /* ===== CARDS & FORMS ===== */
        .card { border-radius: 12px; border: 0.5px solid #D3D1C7; }
        .form-control.rounded-pill, .form-select.rounded-pill { border-radius: 2rem !important; }
        .btn.rounded-pill { border-radius: 2rem !important; }

        /* ===== MOBILE ===== */
        .mobile-menu-btn { display: none; }
        .mobile-sidebar {
            position: fixed;
            top: 0; left: -280px;
            width: 280px; height: 100vh;
            background: #0C447C;
            z-index: 1070;
            transition: left 0.3s;
            overflow-y: auto;
        }
        .mobile-sidebar.open { left: 0; }
        .mobile-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1065;
            display: none;
        }
        .mobile-overlay.open { display: block; }
        .mobile-nav-link {
            color: rgba(255,255,255,0.75);
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            text-decoration: none;
            border-radius: 8px;
            margin: 2px 8px;
        }
        .mobile-nav-link:hover, .mobile-nav-link.active { background: rgba(255,255,255,0.15); color: white; }
        .mobile-section-title {
            color: rgba(255,255,255,0.4);
            font-size: 10px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 12px 16px 4px;
        }

        @media (max-width: 767px) {
            .mobile-menu-btn { display: flex; }
            .nav-sub { display: none; }
            .main-content { margin-top: 70px; padding: 12px; }
            .search-btn span { display: none; }
        }

        @media (min-width: 768px) {
            .mobile-sidebar { display: none; }
        }

        /* ===== SPINNER ===== */
        .spinner-border-sm { width: 1rem; height: 1rem; border-width: 0.15em; }
    </style>
    @stack('styles')
</head>
<body>

{{-- TOP NAVBAR --}}
<div class="nav-top">
    <div style="display:flex;align-items:center;gap:10px;">
        <button class="mobile-menu-btn btn btn-link text-white p-0" onclick="toggleMobileSidebar()" style="font-size:18px;">
            <i class="fas fa-bars"></i>
        </button>
        <a class="nav-brand" href="{{ url('/') }}">
            <div class="nav-brand-icon">
                <i class="fas fa-hospital-alt"></i>
            </div>
            <div class="nav-brand-text">
                <span class="nav-brand-title">ระบบสต็อกยาและเวชภัณฑ์</span>
                <span class="nav-brand-sub">โรงพยาบาลวัดห้วยปลากั้งเพื่อสังคม</span>
            </div>
        </a>
    </div>
    <div class="nav-right">
        <div class="search-btn">
            <i class="fas fa-search"></i>
            <span>ค้นหาในระบบ...</span>
        </div>
        @auth
        <div class="user-dropdown">
            <div class="user-btn" onclick="toggleUserMenu()">
                <div class="avatar">
                    {{ strtoupper(substr(Auth::user()->fullname ?? Auth::user()->username, 0, 2)) }}
                </div>
                <span>{{ Auth::user()->fullname ?? Auth::user()->username }}</span>
                <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.8;"></i>
            </div>
            <div class="dropdown-menu-custom" id="userDropdown">
                <div class="drop-role">
                    <i class="fas fa-shield-alt me-1"></i>
                    บทบาท: {{ ucfirst(Auth::user()->role) }}
                </div>
                <a class="drop-item" href="#">
                    <i class="fas fa-user" style="width:16px;color:#5F5E5A;"></i> โปรไฟล์
                </a>
                <a class="drop-item" href="#">
                    <i class="fas fa-key" style="width:16px;color:#5F5E5A;"></i> เปลี่ยนรหัสผ่าน
                </a>
                <div class="drop-divider"></div>
                <a class="drop-item danger" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt" style="width:16px;"></i> ออกจากระบบ
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </div>
        @endauth
    </div>
</div>

{{-- SUB NAVBAR --}}
<div class="nav-sub">
    <a class="nav-sub-item {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <i class="fas fa-tachometer-alt" style="font-size:13px;"></i> Dashboard
    </a>

    @if(Auth::check() && in_array(Auth::user()->role, ['admin','manager']))
    <div class="nav-sub-item {{ Request::routeIs('departments.*','categories.*','suppliers.*','manufacturers.*','locations.*','product-types.*','products.*','batches.*') ? 'active' : '' }}">
        <span style="display:flex;align-items:center;gap:6px;height:100%;padding:0 16px;cursor:pointer;">
            <i class="fas fa-database" style="font-size:13px;"></i> ข้อมูลหลัก
            <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.7;"></i>
        </span>
        <div class="nav-sub-dropdown">
            <a href="{{ route('departments.index') }}" class="{{ Request::routeIs('departments.*') ? 'active' : '' }}">
                <i class="fas fa-building" style="width:16px;color:#5F5E5A;"></i> แผนก (Departments)
            </a>
            <a href="{{ route('categories.index') }}" class="{{ Request::routeIs('categories.*') ? 'active' : '' }}">
                <i class="fas fa-tags" style="width:16px;color:#5F5E5A;"></i> หมวดหมู่ (Categories)
            </a>
            <a href="{{ route('suppliers.index') }}" class="{{ Request::routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="fas fa-truck" style="width:16px;color:#5F5E5A;"></i> ผู้จัดจำหน่าย (Suppliers)
            </a>
            <a href="{{ route('manufacturers.index') }}" class="{{ Request::routeIs('manufacturers.*') ? 'active' : '' }}">
                <i class="fas fa-industry" style="width:16px;color:#5F5E5A;"></i> ผู้ผลิต (Manufacturers)
            </a>
            <a href="{{ route('locations.index') }}" class="{{ Request::routeIs('locations.*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt" style="width:16px;color:#5F5E5A;"></i> ตำแหน่งสินค้า
            </a>
            <a href="{{ route('product-types.index') }}" class="{{ Request::routeIs('product-types.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group" style="width:16px;color:#5F5E5A;"></i> ประเภทสินค้า (Product Types)
            </a>
            <a href="{{ route('products.index') }}" class="{{ Request::routeIs('products.*') ? 'active' : '' }}">
                <i class="fas fa-box" style="width:16px;color:#5F5E5A;"></i> สินค้า (Products)
            </a>
            <a href="{{ route('batches.index') }}" class="{{ Request::routeIs('batches.*') ? 'active' : '' }}">
                <i class="fas fa-boxes" style="width:16px;color:#5F5E5A;"></i> ล็อตสินค้า (Batches)
            </a>
        </div>
    </div>
    @endif

    <div class="nav-sub-item {{ Request::routeIs('stock_transactions.*') ? 'active' : '' }}">
        <span style="display:flex;align-items:center;gap:6px;height:100%;padding:0 16px;cursor:pointer;">
            <i class="fas fa-boxes" style="font-size:13px;"></i> การจัดการสต็อก
            <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.7;"></i>
        </span>
        <div class="nav-sub-dropdown">
            <a href="{{ route('stock_transactions.index') }}" class="{{ Request::routeIs('stock_transactions.index') ? 'active' : '' }}">
                <i class="fas fa-history" style="width:16px;color:#5F5E5A;"></i> รายการสต็อก
            </a>
            @if(Auth::check() && in_array(Auth::user()->role, ['admin','manager']))
            <a href="{{ route('stock_transactions.receive.create') }}" class="{{ Request::routeIs('stock_transactions.receive.*') ? 'active' : '' }}">
                <i class="fas fa-arrow-circle-down" style="width:16px;color:#3B6D11;"></i> รับเข้าสินค้า
            </a>
            <a href="{{ route('stock_transactions.issue.create') }}" class="{{ Request::routeIs('stock_transactions.issue.*') ? 'active' : '' }}">
                <i class="fas fa-arrow-circle-up" style="width:16px;color:#A32D2D;"></i> จ่ายออกสินค้า
            </a>
            <a href="{{ route('stock_transactions.adjust.create') }}" class="{{ Request::routeIs('stock_transactions.adjust.*') ? 'active' : '' }}">
                <i class="fas fa-sliders-h" style="width:16px;color:#854F0B;"></i> ปรับปรุงสต็อก
            </a>
            @endif
            <a href="{{ route('scanner.index') }}" class="{{ Request::routeIs('scanner.*') ? 'active' : '' }}">
                <i class="fas fa-barcode" style="width:16px;color:#5F5E5A;"></i> สแกนบาร์โค้ด
            </a>
        </div>
    </div>

    <div class="nav-sub-item {{ Request::routeIs('requisitions.*') ? 'active' : '' }}">
        <span style="display:flex;align-items:center;gap:6px;height:100%;padding:0 16px;cursor:pointer;">
            <i class="fas fa-file-invoice" style="font-size:13px;"></i> จัดการใบขอเบิก
            <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.7;"></i>
        </span>
        <div class="nav-sub-dropdown">
            <a href="{{ route('requisitions.index') }}" class="{{ Request::routeIs('requisitions.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice" style="width:16px;color:#5F5E5A;"></i> รายการใบขอเบิก
            </a>
        </div>
    </div>

    @if(Auth::check() && Auth::user()->role === 'admin')
    <div class="nav-sub-item {{ Request::routeIs('reports.*') ? 'active' : '' }}">
        <span style="display:flex;align-items:center;gap:6px;height:100%;padding:0 16px;cursor:pointer;">
            <i class="fas fa-chart-bar" style="font-size:13px;"></i> รายงาน
            <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.7;"></i>
        </span>
        <div class="nav-sub-dropdown">
            <a href="{{ route('reports.stock') }}" class="{{ Request::routeIs('reports.stock') ? 'active' : '' }}">
                <i class="fas fa-chart-pie" style="width:16px;color:#5F5E5A;"></i> รายงานสต็อก
            </a>
            <a href="{{ route('reports.requisition') }}" class="{{ Request::routeIs('reports.requisition') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list" style="width:16px;color:#5F5E5A;"></i> รายงานการเบิก
            </a>
            <a href="{{ route('reports.low_stock_products') }}" class="{{ Request::routeIs('reports.low_stock_products') ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle" style="width:16px;color:#A32D2D;"></i> สินค้าสต็อกต่ำ
            </a>
            <a href="{{ route('reports.expiring_batches') }}" class="{{ Request::routeIs('reports.expiring_batches') ? 'active' : '' }}">
                <i class="fas fa-calendar-times" style="width:16px;color:#854F0B;"></i> ล็อตใกล้หมดอายุ
            </a>
            <a href="{{ route('reports.pending_requisitions') }}" class="{{ Request::routeIs('reports.pending_requisitions') ? 'active' : '' }}">
                <i class="fas fa-hourglass-half" style="width:16px;color:#5F5E5A;"></i> ใบขอเบิกค้างอนุมัติ
            </a>
        </div>
    </div>

    <div class="nav-sub-item {{ Request::routeIs('users.*') ? 'active' : '' }}">
        <span style="display:flex;align-items:center;gap:6px;height:100%;padding:0 16px;cursor:pointer;">
            <i class="fas fa-cog" style="font-size:13px;"></i> ตั้งค่าระบบ
            <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.7;"></i>
        </span>
        <div class="nav-sub-dropdown">
            <a href="{{ route('users.index') }}" class="{{ Request::routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog" style="width:16px;color:#5F5E5A;"></i> จัดการผู้ใช้งาน
            </a>
        </div>
    </div>
    @endif
</div>

{{-- MOBILE SIDEBAR OVERLAY --}}
<div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileSidebar()"></div>

{{-- MOBILE SIDEBAR --}}
<div class="mobile-sidebar" id="mobileSidebar">
    <div style="padding:16px;border-bottom:0.5px solid rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:space-between;">
        <span style="color:white;font-size:14px;font-weight:500;"><i class="fas fa-hospital-alt me-2"></i>เมนูหลัก</span>
        <button onclick="toggleMobileSidebar()" class="btn btn-link text-white p-0"><i class="fas fa-times"></i></button>
    </div>
    <div style="padding:8px 0;">
        <a class="mobile-nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt" style="width:18px;"></i> Dashboard
        </a>

        @if(Auth::check() && in_array(Auth::user()->role, ['admin','manager']))
        <div class="mobile-section-title">ข้อมูลหลัก</div>
        <a class="mobile-nav-link {{ Request::routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}"><i class="fas fa-building" style="width:18px;"></i> แผนก</a>
        <a class="mobile-nav-link {{ Request::routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="fas fa-tags" style="width:18px;"></i> หมวดหมู่</a>
        <a class="mobile-nav-link {{ Request::routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><i class="fas fa-truck" style="width:18px;"></i> ผู้จัดจำหน่าย</a>
        <a class="mobile-nav-link {{ Request::routeIs('manufacturers.*') ? 'active' : '' }}" href="{{ route('manufacturers.index') }}"><i class="fas fa-industry" style="width:18px;"></i> ผู้ผลิต</a>
        <a class="mobile-nav-link {{ Request::routeIs('locations.*') ? 'active' : '' }}" href="{{ route('locations.index') }}"><i class="fas fa-map-marker-alt" style="width:18px;"></i> ตำแหน่งสินค้า</a>
        <a class="mobile-nav-link {{ Request::routeIs('product-types.*') ? 'active' : '' }}" href="{{ route('product-types.index') }}"><i class="fas fa-layer-group" style="width:18px;"></i> ประเภทสินค้า</a>
        <a class="mobile-nav-link {{ Request::routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}"><i class="fas fa-box" style="width:18px;"></i> สินค้า</a>
        <a class="mobile-nav-link {{ Request::routeIs('batches.*') ? 'active' : '' }}" href="{{ route('batches.index') }}"><i class="fas fa-boxes" style="width:18px;"></i> ล็อตสินค้า</a>
        @endif

        <div class="mobile-section-title">การจัดการสต็อก</div>
        <a class="mobile-nav-link {{ Request::routeIs('stock_transactions.index') ? 'active' : '' }}" href="{{ route('stock_transactions.index') }}"><i class="fas fa-history" style="width:18px;"></i> รายการสต็อก</a>
        @if(Auth::check() && in_array(Auth::user()->role, ['admin','manager']))
        <a class="mobile-nav-link" href="{{ route('stock_transactions.receive.create') }}"><i class="fas fa-arrow-circle-down" style="width:18px;"></i> รับเข้าสินค้า</a>
        <a class="mobile-nav-link" href="{{ route('stock_transactions.issue.create') }}"><i class="fas fa-arrow-circle-up" style="width:18px;"></i> จ่ายออกสินค้า</a>
        <a class="mobile-nav-link" href="{{ route('stock_transactions.adjust.create') }}"><i class="fas fa-sliders-h" style="width:18px;"></i> ปรับปรุงสต็อก</a>
        @endif
        <a class="mobile-nav-link" href="{{ route('scanner.index') }}"><i class="fas fa-barcode" style="width:18px;"></i> สแกนบาร์โค้ด</a>

        <div class="mobile-section-title">ใบขอเบิก</div>
        <a class="mobile-nav-link {{ Request::routeIs('requisitions.*') ? 'active' : '' }}" href="{{ route('requisitions.index') }}"><i class="fas fa-file-invoice" style="width:18px;"></i> รายการใบขอเบิก</a>

        @if(Auth::check() && Auth::user()->role === 'admin')
        <div class="mobile-section-title">รายงาน</div>
        <a class="mobile-nav-link" href="{{ route('reports.stock') }}"><i class="fas fa-chart-pie" style="width:18px;"></i> รายงานสต็อก</a>
        <a class="mobile-nav-link" href="{{ route('reports.requisition') }}"><i class="fas fa-clipboard-list" style="width:18px;"></i> รายงานการเบิก</a>
        <a class="mobile-nav-link" href="{{ route('reports.low_stock_products') }}"><i class="fas fa-exclamation-triangle" style="width:18px;"></i> สินค้าสต็อกต่ำ</a>
        <a class="mobile-nav-link" href="{{ route('reports.expiring_batches') }}"><i class="fas fa-calendar-times" style="width:18px;"></i> ล็อตใกล้หมดอายุ</a>
        <a class="mobile-nav-link" href="{{ route('reports.pending_requisitions') }}"><i class="fas fa-hourglass-half" style="width:18px;"></i> ใบขอเบิกค้างอนุมัติ</a>

        <div class="mobile-section-title">ตั้งค่าระบบ</div>
        <a class="mobile-nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="fas fa-users-cog" style="width:18px;"></i> จัดการผู้ใช้งาน</a>
        @endif
    </div>
</div>

{{-- MAIN CONTENT --}}
<main class="main-content">
    <div class="container-fluid px-0">
        @if(session('success'))
        <div class="alert-custom alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-check-circle"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert-custom alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-times-circle"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif
    </div>
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleUserMenu() {
    document.getElementById('userDropdown').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.user-dropdown')) {
        const dd = document.getElementById('userDropdown');
        if (dd) dd.classList.remove('open');
    }
});
function toggleMobileSidebar() {
    document.getElementById('mobileSidebar').classList.toggle('open');
    document.getElementById('mobileOverlay').classList.toggle('open');
}
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });

    // Dropdown submenu with fixed positioning
    document.querySelectorAll('.nav-sub-item > span').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const parent = this.closest('.nav-sub-item');
            const dropdown = parent.querySelector('.nav-sub-dropdown');
            if (!dropdown) return;

            // ปิด dropdown อื่นทั้งหมด
            document.querySelectorAll('.nav-sub-dropdown.show').forEach(function(d) {
                if (d !== dropdown) d.classList.remove('show');
            });

            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            } else {
                // คำนวณตำแหน่งจาก parent element
                const rect = parent.getBoundingClientRect();
                dropdown.style.top = rect.bottom + 'px';
                dropdown.style.left = rect.left + 'px';
                dropdown.classList.add('show');
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-sub-item')) {
            document.querySelectorAll('.nav-sub-dropdown.show').forEach(function(d) {
                d.classList.remove('show');
            });
        }
    });
});
</script>
@stack('scripts')
</body>
</html>