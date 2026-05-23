@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
/* Hero */
.hero-section {
    background: #0C447C;
    margin: -20px -20px 0 -20px;
    padding: 20px 20px 40px;
    position: relative;
    overflow: hidden;
}
.hero-section::before {
    content: '';
    position: absolute;
    right: -60px; top: -60px;
    width: 250px; height: 250px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
}
.hero-section::after {
    content: '';
    position: absolute;
    right: 80px; bottom: -80px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,0.03);
}
.hero-stats { display: flex; gap: 24px; }
.hero-stat { text-align: center; }
.hero-stat-val { font-size: 22px; font-weight: 500; color: white; line-height: 1; }
.hero-stat-label { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 3px; }
.hero-divider { width: 0.5px; background: rgba(255,255,255,0.15); align-self: stretch; }

/* Cards */
.dash-cards {
    display: grid;
    grid-template-columns: repeat(4,minmax(0,1fr));
    gap: 12px;
    margin: -22px 0 14px;
    position: relative;
    z-index: 1;
}
.dash-card {
    background: white;
    border: 0.5px solid #D3D1C7;
    border-radius: 12px;
    padding: 16px;
    text-decoration: none;
    display: block;
    position: relative;
    overflow: hidden;
    transition: opacity 0.15s;
}
.dash-card:hover { opacity: 0.92; border-color: #B4B2A9; }
.dash-card-accent {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 12px 12px 0 0;
}
.dash-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    margin-top: 6px;
}
.dash-card-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}
.dash-card-badge {
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 99px;
    font-weight: 500;
}
.dash-card-val {
    font-size: 28px;
    font-weight: 500;
    line-height: 1;
    margin-bottom: 3px;
}
.dash-card-label {
    font-size: 11px;
    color: #888780;
    margin-bottom: 12px;
}
.dash-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 10px;
    border-top: 0.5px solid #E0DDD5;
    font-size: 10px;
    color: #888780;
}
.dash-card-link {
    display: flex;
    align-items: center;
    gap: 3px;
    font-size: 10px;
}

/* Panels */
.panel {
    background: white;
    border: 0.5px solid #D3D1C7;
    border-radius: 12px;
    overflow: hidden;
}
.panel-hd {
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 0.5px solid #E0DDD5;
    background: #F7F5EF;
}
.panel-title { font-size: 12px; font-weight: 500; color: #2C2C2A; flex: 1; }
.panel-link {
    font-size: 10px;
    color: #185FA5;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 3px;
    padding: 3px 8px;
    background: #E6F1FB;
    border-radius: 99px;
}
.panel-link:hover { background: #B5D4F4; }
.panel-body { padding: 12px 14px; }

/* Chart */
.chart-area {
    display: flex;
    align-items: flex-end;
    gap: 6px;
    height: 110px;
    margin-bottom: 6px;
    position: relative;
}
.chart-area::after {
    content: '';
    position: absolute;
    left: 0; right: 0; bottom: 0;
    height: 0.5px;
    background: #E0DDD5;
}
.bar-group { flex: 1; display: flex; gap: 2px; align-items: flex-end; height: 100%; }
.bar { flex: 1; border-radius: 3px 3px 0 0; min-height: 3px; }
.chart-x { display: flex; gap: 6px; margin-bottom: 8px; }
.chart-x-label { flex: 1; font-size: 9px; color: #888780; text-align: center; }
.chart-legend { display: flex; gap: 12px; }
.legend-item { display: flex; align-items: center; gap: 4px; font-size: 10px; color: #5F5E5A; }
.legend-dot { width: 8px; height: 8px; border-radius: 2px; }

/* Activity Feed */
.feed-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 9px 0;
    border-bottom: 0.5px solid #F0EDE6;
}
.feed-item:last-child { border-bottom: none; }
.feed-icon {
    width: 30px; height: 30px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 13px;
}
.feed-name { font-size: 12px; color: #2C2C2A; font-weight: 500; }
.feed-sub { font-size: 10px; color: #888780; margin-top: 2px; }
.feed-qty { font-size: 12px; font-weight: 500; }
.feed-time { font-size: 10px; color: #888780; margin-top: 2px; }

/* Table */
.tbl { width: 100%; border-collapse: collapse; font-size: 12px; }
.tbl thead tr { background: #F7F5EF; }
.tbl th { font-size: 11px; color: #888780; font-weight: 500; padding: 8px 12px; text-align: left; border-bottom: 0.5px solid #D3D1C7; }
.tbl td { padding: 9px 12px; border-bottom: 0.5px solid #F0EDE6; color: #2C2C2A; vertical-align: middle; }
.tbl tr:last-child td { border-bottom: none; }
.tbl tbody tr:hover td { background: #FAFAF8; }
.badge { font-size: 11px; padding: 3px 9px; border-radius: 4px; font-weight: 500; }
.b-in { background: #EAF3DE; color: #27500A; }
.b-out { background: #FCEBEB; color: #791F1F; }
.b-adj-in { background: #E6F1FB; color: #0C447C; }
.b-adj-out { background: #FAEEDA; color: #633806; }

/* Mini stats */
.mini-grid {
    display: grid;
    grid-template-columns: repeat(4,minmax(0,1fr));
    gap: 10px;
    margin-top: 12px;
}
.mini-stat {
    background: white;
    border: 0.5px solid #D3D1C7;
    border-radius: 12px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.mini-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
}
.mini-val { font-size: 18px; font-weight: 500; color: #2C2C2A; line-height: 1; }
.mini-label { font-size: 10px; color: #888780; margin-top: 2px; }
</style>

{{-- HERO SECTION --}}
<div class="hero-section">
    <div style="font-size:14px;color:rgba(255,255,255,0.45);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:3px;">ภาพรวมระบบ</div>
    <div style="font-size:24px;font-weight:500;color:white;margin-bottom:14px;">
        โรงพยาบาลวัดห้วยปลากั้งเพื่อสังคม
        <span style="font-size:14px;color:rgba(255,255,255,0.5);font-weight:400;margin-left:8px;">
            {{ \Carbon\Carbon::now()->timezone('Asia/Bangkok')->isoFormat('D MMMM YYYY') }}
        </span>
    </div>
    <div class="hero-stats">
        <div class="hero-stat">
            <div class="hero-stat-val">{{ number_format($totalProducts) }}</div>
            <div class="hero-stat-label">สินค้าทั้งหมด</div>
        </div>
        <div class="hero-divider"></div>
        <div class="hero-stat">
            <div class="hero-stat-val">{{ number_format($totalBatches) }}</div>
            <div class="hero-stat-label">ล็อตสินค้า</div>
        </div>
        <div class="hero-divider"></div>
        <div class="hero-stat">
            <div class="hero-stat-val" style="color:#FAC775;">{{ number_format($lowStockProductsCount) }}</div>
            <div class="hero-stat-label">สต็อกต่ำ</div>
        </div>
        <div class="hero-divider"></div>
        <div class="hero-stat">
            <div class="hero-stat-val" style="color:#F09595;">{{ number_format($expiringBatchesCount) }}</div>
            <div class="hero-stat-label">ใกล้หมดอายุ</div>
        </div>
        <div class="hero-divider"></div>
        <div class="hero-stat">
            <div class="hero-stat-val" style="color:#9FE1CB;">{{ number_format($pendingRequisitionsCount) }}</div>
            <div class="hero-stat-label">รอดำเนินการ</div>
        </div>
    </div>
</div>

{{-- SUMMARY CARDS --}}
<div class="dash-cards">
    <a href="{{ route('products.index') }}" class="dash-card">
        <div class="dash-card-accent" style="background:#185FA5;"></div>
        <div class="dash-card-top">
            <div class="dash-card-icon" style="background:#E6F1FB;">
                <i class="fas fa-boxes" style="color:#185FA5;"></i>
            </div>
            <span class="dash-card-badge" style="background:#E6F1FB;color:#0C447C;">+12 เดือนนี้</span>
        </div>
        <div class="dash-card-val" style="color:#0C447C;">{{ number_format($totalProducts) }}</div>
        <div class="dash-card-label">รายการสินค้าทั้งหมด</div>
        <div class="dash-card-footer">
            <span>ล็อตสินค้า {{ number_format($totalBatches) }} ล็อต</span>
            <span class="dash-card-link" style="color:#185FA5;">ดูทั้งหมด <i class="fas fa-arrow-right" style="font-size:9px;"></i></span>
        </div>
    </a>

    <a href="{{ route('reports.low_stock_products') }}" class="dash-card">
        <div class="dash-card-accent" style="background:#A32D2D;"></div>
        <div class="dash-card-top">
            <div class="dash-card-icon" style="background:#FCEBEB;">
                <i class="fas fa-exclamation-triangle" style="color:#A32D2D;"></i>
            </div>
            <span class="dash-card-badge" style="background:#FCEBEB;color:#791F1F;">ต้องสั่งซื้อ</span>
        </div>
        <div class="dash-card-val" style="color:#791F1F;">{{ number_format($lowStockProductsCount) }}</div>
        <div class="dash-card-label">สต็อกต่ำกว่าจุดต่ำสุด</div>
        <div class="dash-card-footer">
            <span>อัปเดตวันนี้</span>
            <span class="dash-card-link" style="color:#A32D2D;">ดูรายละเอียด <i class="fas fa-arrow-right" style="font-size:9px;"></i></span>
        </div>
    </a>

    <a href="{{ route('reports.expiring_batches') }}" class="dash-card">
        <div class="dash-card-accent" style="background:#854F0B;"></div>
        <div class="dash-card-top">
            <div class="dash-card-icon" style="background:#FAEEDA;">
                <i class="fas fa-calendar-times" style="color:#854F0B;"></i>
            </div>
            <span class="dash-card-badge" style="background:#FAEEDA;color:#633806;">30 วัน</span>
        </div>
        <div class="dash-card-val" style="color:#633806;">{{ number_format($expiringBatchesCount) }}</div>
        <div class="dash-card-label">ล็อตใกล้หมดอายุ</div>
        <div class="dash-card-footer">
            <span>ภายใน 30 วันข้างหน้า</span>
            <span class="dash-card-link" style="color:#854F0B;">ดูรายละเอียด <i class="fas fa-arrow-right" style="font-size:9px;"></i></span>
        </div>
    </a>

    <a href="{{ route('requisitions.index') }}" class="dash-card">
        <div class="dash-card-accent" style="background:#3B6D11;"></div>
        <div class="dash-card-top">
            <div class="dash-card-icon" style="background:#EAF3DE;">
                <i class="fas fa-file-invoice" style="color:#3B6D11;"></i>
            </div>
            <span class="dash-card-badge" style="background:#EAF3DE;color:#27500A;">Pending</span>
        </div>
        <div class="dash-card-val" style="color:#27500A;">{{ number_format($pendingRequisitionsCount) }}</div>
        <div class="dash-card-label">ใบขอเบิกค้างอนุมัติ</div>
        <div class="dash-card-footer">
            <span>รออนุมัติ {{ number_format($pendingRequisitionsCount) }} รายการ</span>
            <span class="dash-card-link" style="color:#3B6D11;">ดูรายละเอียด <i class="fas fa-arrow-right" style="font-size:9px;"></i></span>
        </div>
    </a>
</div>

{{-- MID SECTION --}}
<div style="display:grid;grid-template-columns:5fr 3fr;gap:12px;margin-bottom:12px;">

    {{-- Department Bar Chart --}}
    <div class="panel">
        <div class="panel-hd">
            <i class="fas fa-chart-bar" style="color:#185FA5;font-size:14px;"></i>
            <span class="panel-title">การเบิกใช้สินค้าแยกตามแผนก</span>
            <span style="font-size:10px;background:#E6F1FB;color:#0C447C;padding:2px 8px;border-radius:99px;">จำนวนจ่ายออกรวม</span>
        </div>
        <div class="panel-body">
            @php
                $deptColors = ['#185FA5','#A32D2D','#854F0B','#3B6D11','#0F6E56','#533AB7','#993556','#D85A30'];
                $maxDept = $departmentStats->max('total') ?: 1;
            @endphp

            {{-- Chart --}}
            <div style="display:flex;align-items:flex-end;gap:6px;height:110px;margin-bottom:6px;position:relative;">
                <div style="position:absolute;left:0;right:0;bottom:0;height:0.5px;background:#E0DDD5;"></div>
                @foreach($departmentStats as $i => $dept)
                @php
                    $color = $deptColors[$i % count($deptColors)];
                    $heightPct = round(($dept['total'] / $maxDept) * 100);
                    $shortName = mb_strlen($dept['name']) > 6
                        ? mb_substr($dept['name'], 0, 6) . '...'
                        : $dept['name'];
                @endphp
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;height:100%;">
                    <div style="font-size:9px;color:{{ $color }};font-weight:500;margin-bottom:3px;">
                        {{ number_format($dept['total']) }}
                    </div>
                    <div style="flex:1;width:100%;display:flex;align-items:flex-end;">
                        <div style="width:100%;height:{{ $heightPct }}%;background:{{ $color }};border-radius:3px 3px 0 0;min-height:3px;"></div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- X Labels --}}
            <div style="display:flex;gap:6px;margin-bottom:10px;">
                @foreach($departmentStats as $i => $dept)
                @php
                    $color = $deptColors[$i % count($deptColors)];
                    $words = explode(' ', $dept['name']);
                    $shortName = mb_strlen($words[0]) > 8 ? mb_substr($words[0], 0, 8) : $words[0];
                @endphp
                <div style="flex:1;font-size:9px;color:{{ $color }};text-align:center;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ $shortName }}
                </div>
                @endforeach
            </div>

            {{-- Legend --}}
            <div style="display:flex;flex-wrap:wrap;gap:6px 12px;">
                @foreach($departmentStats as $i => $dept)
                @php $color = $deptColors[$i % count($deptColors)]; @endphp
                <div style="display:flex;align-items:center;gap:4px;">
                    <div style="width:8px;height:8px;border-radius:2px;background:{{ $color }};flex-shrink:0;"></div>
                    <span style="font-size:10px;color:#5F5E5A;white-space:nowrap;">{{ $dept['name'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Activity Feed --}}
    <div class="panel">
        <div class="panel-hd">
            <i class="fas fa-history" style="color:#185FA5;font-size:14px;"></i>
            <span class="panel-title">รายการล่าสุด</span>
            <a href="{{ route('stock_transactions.index') }}" class="panel-link">ดูทั้งหมด <i class="fas fa-arrow-right" style="font-size:9px;"></i></a>
        </div>
        <div class="panel-body">
            @forelse($recentTransactions as $transaction)
            @php
                $isOut = in_array($transaction->transaction_type, ['out','adjustment_out']);
                $isIn = in_array($transaction->transaction_type, ['in','adjustment_in']);
            @endphp
            <div class="feed-item">
                <div class="feed-icon" style="background:{{ $isIn ? '#EAF3DE' : ($isOut ? '#FCEBEB' : '#FAEEDA') }};">
                    <i class="fas fa-{{ $isIn ? 'arrow-down' : ($isOut ? 'arrow-up' : 'sliders-h') }}"
                       style="color:{{ $isIn ? '#3B6D11' : ($isOut ? '#A32D2D' : '#854F0B') }};"></i>
                </div>
                <div style="flex:1;overflow:hidden;">
                    <div class="feed-name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $transaction->product->name ?? '-' }}
                    </div>
                    <div class="feed-sub">
                        {{ $transaction->department->name ?? '-' }} · {{ $transaction->batch->batch_number ?? '-' }}
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div class="feed-qty" style="color:{{ $isOut ? '#A32D2D' : '#3B6D11' }};">
                        {{ $isOut ? '-' : '+' }}{{ number_format($transaction->quantity) }}
                    </div>
                    <div class="feed-time">
                        {{ \Carbon\Carbon::parse($transaction->created_at)->timezone('Asia/Bangkok')->format('H:i') }}
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center;color:#888780;font-size:12px;padding:20px 0;">ไม่มีรายการ</div>
            @endforelse
        </div>
    </div>
</div>

{{-- LOW STOCK TABLE --}}
<div class="panel" style="margin-bottom:12px;">
    <div class="panel-hd">
        <i class="fas fa-exclamation-triangle" style="color:#A32D2D;font-size:14px;"></i>
        <span class="panel-title">สินค้าสต็อกต่ำกว่าจุดต่ำสุด — ต้องสั่งซื้อ</span>
        <a href="{{ route('reports.low_stock_products') }}" class="panel-link" style="background:#FCEBEB;color:#A32D2D;">
            ดูทั้งหมด <i class="fas fa-arrow-right" style="font-size:9px;"></i>
        </a>
    </div>
    <table class="tbl">
        <thead>
            <tr>
                <th>#</th>
                <th>รหัสสินค้า</th>
                <th>ชื่อสินค้า</th>
                <th>หมวดหมู่</th>
                <th>ผู้จัดจำหน่าย</th>
                <th style="width:80px;">คงเหลือ</th>
                <th style="width:80px;">จุดต่ำสุด</th>
                <th style="width:90px;">จำนวนสั่งซื้อ</th>
                <th style="width:60px;">หน่วย</th>
                <th style="width:80px;">สถานะ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lowStockTable as $i => $product)
            <tr>
                <td style="color:#888780;">{{ $i + 1 }}</td>
                <td style="color:#185FA5;font-weight:500;">{{ $product->product_code }}</td>
                <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ $product->name }}
                </td>
                <td style="color:#888780;">{{ $product->category->name ?? '-' }}</td>
                <td style="color:#888780;">{{ $product->supplier->name ?? '-' }}</td>
                <td style="font-weight:500;color:{{ $product->current_stock == 0 ? '#791F1F' : '#633806' }};">
                    {{ number_format($product->current_stock) }}
                </td>
                <td style="color:#888780;">{{ number_format($product->minimum_stock_level) }}</td>
                <td style="font-weight:500;color:#185FA5;">{{ number_format($product->order_qty) }}</td>
                <td style="color:#888780;">{{ $product->unit }}</td>
                <td>
                    @if($product->current_stock == 0)
                        <span class="badge b-out">หมดแล้ว</span>
                    @else
                        <span class="badge b-adj-out">ใกล้หมด</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align:center;color:#888780;padding:20px;">
                    <i class="fas fa-check-circle" style="color:#3B6D11;margin-right:6px;"></i>
                    ไม่มีสินค้าสต็อกต่ำ
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- BOTTOM MINI STATS --}}
<div class="mini-grid">
    <div class="mini-stat">
        <div class="mini-icon" style="background:#E6F1FB;"><i class="fas fa-warehouse" style="color:#185FA5;"></i></div>
        <div><div class="mini-val">{{ number_format($totalStockQuantity) }}</div><div class="mini-label">สต็อกรวม (หน่วย)</div></div>
    </div>
    <div class="mini-stat">
        <div class="mini-icon" style="background:#EAF3DE;"><i class="fas fa-building" style="color:#3B6D11;"></i></div>
        <div><div class="mini-val">{{ number_format($totalDepartments) }}</div><div class="mini-label">แผนกทั้งหมด</div></div>
    </div>
    <div class="mini-stat">
        <div class="mini-icon" style="background:#FAEEDA;"><i class="fas fa-truck" style="color:#854F0B;"></i></div>
        <div><div class="mini-val">{{ number_format($totalSuppliers) }}</div><div class="mini-label">ผู้จัดจำหน่าย</div></div>
    </div>
    <div class="mini-stat">
        <div class="mini-icon" style="background:#E6F1FB;"><i class="fas fa-users" style="color:#185FA5;"></i></div>
        <div><div class="mini-val">{{ number_format($totalUsers) }}</div><div class="mini-label">ผู้ใช้งาน</div></div>
    </div>
</div>
@endsection