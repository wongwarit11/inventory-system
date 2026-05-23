@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
.stat-card {
    background: white;
    border: 0.5px solid #D3D1C7;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.stat-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}
.stat-val { font-size: 26px; font-weight: 600; color: #2C2C2A; line-height: 1; }
.stat-label { font-size: 12px; color: #888780; }
.stat-badge { font-size: 11px; padding: 2px 8px; border-radius: 99px; display: inline-block; }
.panel {
    background: white;
    border: 0.5px solid #D3D1C7;
    border-radius: 12px;
    padding: 16px;
}
.panel-title {
    font-size: 13px; font-weight: 500; color: #2C2C2A;
    display: flex; align-items: center; gap: 6px;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 0.5px solid #E0DDD5;
}
.bar-wrap { display: flex; flex-direction: column; gap: 10px; }
.bar-row { display: flex; align-items: center; gap: 8px; }
.bar-day { font-size: 11px; color: #888780; width: 40px; text-align: right; flex-shrink: 0; }
.bar-track { flex: 1; background: #f0f4f8; border-radius: 99px; height: 8px; overflow: hidden; }
.bar-fill-out { height: 8px; border-radius: 99px; background: #185FA5; }
.bar-num { font-size: 11px; color: #888780; width: 30px; text-align: right; flex-shrink: 0; }
.low-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 10px;
    background: #f7f5ef;
    border-radius: 8px;
    margin-bottom: 6px;
}
.low-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.tbl { width: 100%; border-collapse: collapse; font-size: 12px; }
.tbl th { font-size: 11px; color: #888780; font-weight: 500; padding: 6px 10px; text-align: left; border-bottom: 0.5px solid #E0DDD5; }
.tbl td { padding: 8px 10px; border-bottom: 0.5px solid #F0EDE6; color: #2C2C2A; }
.tbl tr:last-child td { border-bottom: none; }
.badge-in { background: #EAF3DE; color: #3B6D11; font-size: 11px; padding: 2px 8px; border-radius: 99px; font-weight: 500; }
.badge-out { background: #FCEBEB; color: #A32D2D; font-size: 11px; padding: 2px 8px; border-radius: 99px; font-weight: 500; }
.badge-adj-in { background: #E6F1FB; color: #185FA5; font-size: 11px; padding: 2px 8px; border-radius: 99px; font-weight: 500; }
.badge-adj-out { background: #FAEEDA; color: #854F0B; font-size: 11px; padding: 2px 8px; border-radius: 99px; font-weight: 500; }
.see-all { font-size: 11px; color: #185FA5; text-decoration: none; }
.see-all:hover { text-decoration: underline; }
</style>

{{-- Page Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div style="font-size:15px;font-weight:500;color:#2C2C2A;display:flex;align-items:center;gap:7px;">
        <i class="fas fa-tachometer-alt" style="color:#185FA5;"></i> Dashboard
    </div>
    <div style="font-size:12px;color:#888780;">
        <i class="fas fa-calendar me-1"></i>
        {{ \Carbon\Carbon::now()->timezone('Asia/Bangkok')->isoFormat('ddddที่ D MMMM YYYY') }}
    </div>
</div>

{{-- Summary Cards --}}
<div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:14px;">
    <div class="stat-card">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div class="stat-icon" style="background:#E6F1FB;">
                <i class="fas fa-boxes" style="color:#185FA5;"></i>
            </div>
            <span class="stat-badge" style="background:#E6F1FB;color:#185FA5;">Active</span>
        </div>
        <div class="stat-val">{{ number_format($totalProducts) }}</div>
        <div class="stat-label">รายการสินค้าทั้งหมด</div>
    </div>

    <div class="stat-card">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div class="stat-icon" style="background:#FCEBEB;">
                <i class="fas fa-exclamation-triangle" style="color:#A32D2D;"></i>
            </div>
            <span class="stat-badge" style="background:#FCEBEB;color:#A32D2D;">ต้องสั่งซื้อ</span>
        </div>
        <div class="stat-val" style="color:#A32D2D;">{{ number_format($lowStockProductsCount) }}</div>
        <div class="stat-label">สินค้าสต็อกต่ำกว่าจุดต่ำสุด</div>
    </div>

    <div class="stat-card">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div class="stat-icon" style="background:#FAEEDA;">
                <i class="fas fa-calendar-times" style="color:#854F0B;"></i>
            </div>
            <span class="stat-badge" style="background:#FAEEDA;color:#854F0B;">ภายใน 30 วัน</span>
        </div>
        <div class="stat-val" style="color:#854F0B;">{{ number_format($expiringBatchesCount) }}</div>
        <div class="stat-label">ล็อตใกล้หมดอายุ</div>
    </div>

    <div class="stat-card">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div class="stat-icon" style="background:#EAF3DE;">
                <i class="fas fa-file-check" style="color:#3B6D11;"></i>
            </div>
            <span class="stat-badge" style="background:#EAF3DE;color:#3B6D11;">Pending</span>
        </div>
        <div class="stat-val">{{ number_format($pendingRequisitionsCount) }}</div>
        <div class="stat-label">ใบขอเบิกรอดำเนินการ</div>
    </div>
</div>

{{-- Mid Section: Chart + Low Stock --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:10px;margin-bottom:14px;">

    {{-- Bar Chart --}}
    <div class="panel">
        <div class="panel-title">
            <i class="fas fa-chart-bar" style="color:#185FA5;font-size:14px;"></i>
            การเบิกใช้สินค้า 7 วันล่าสุด
            <span style="margin-left:auto;font-size:11px;color:#888780;">จำนวน/วัน</span>
        </div>
        <div class="bar-wrap">
            @php $maxOut = max(array_column($chartData, 'out') ?: [1]); @endphp
            @foreach($chartData as $i => $day)
            <div class="bar-row">
                <span class="bar-day">{{ $day['date'] }}</span>
                <div class="bar-track">
                    <div class="bar-fill-out" style="width:{{ $maxOut > 0 ? round(($day['out'] / $maxOut) * 100) : 0 }}%;{{ $i === 6 ? 'background:#85B7EB;' : '' }}"></div>
                </div>
                <span class="bar-num" style="{{ $i === 6 ? 'color:#85B7EB;' : '' }}">{{ $day['out'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Low Stock List --}}
    <div class="panel">
        <div class="panel-title">
            <i class="fas fa-alert-triangle" style="color:#A32D2D;font-size:14px;"></i>
            <i class="fas fa-exclamation-circle" style="color:#A32D2D;font-size:14px;"></i>
            สินค้าสต็อกต่ำ
            <a href="{{ route('reports.low_stock_products') }}" class="see-all" style="margin-left:auto;">ดูทั้งหมด</a>
        </div>
        @forelse($lowStockProducts as $product)
        @php $currentStock = $product->batches->sum('quantity'); @endphp
        <div class="low-item">
            <div style="display:flex;align-items:center;gap:7px;overflow:hidden;">
                <div class="low-dot" style="background:{{ $currentStock == 0 ? '#A32D2D' : '#854F0B' }};flex-shrink:0;"></div>
                <span style="font-size:12px;color:#2C2C2A;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $product->name }}</span>
            </div>
            <span style="font-size:11px;font-weight:500;color:{{ $currentStock == 0 ? '#A32D2D' : '#854F0B' }};flex-shrink:0;margin-left:8px;">
                {{ number_format($currentStock) }} {{ $product->unit }}
            </span>
        </div>
        @empty
        <div style="text-align:center;color:#888780;font-size:12px;padding:20px 0;">
            <i class="fas fa-check-circle" style="color:#3B6D11;font-size:20px;display:block;margin-bottom:6px;"></i>
            ไม่มีสินค้าสต็อกต่ำ
        </div>
        @endforelse
    </div>
</div>

{{-- Recent Transactions --}}
<div class="panel">
    <div class="panel-title">
        <i class="fas fa-history" style="color:#185FA5;font-size:14px;"></i>
        การเคลื่อนไหวสต็อกล่าสุด
        <a href="{{ route('stock_transactions.index') }}" class="see-all" style="margin-left:auto;">ดูทั้งหมด</a>
    </div>
    <table class="tbl">
        <thead>
            <tr>
                <th style="width:110px;">วันที่/เวลา</th>
                <th style="width:80px;">ประเภท</th>
                <th>สินค้า</th>
                <th style="width:80px;">ล็อต</th>
                <th style="width:60px;">จำนวน</th>
                <th style="width:120px;">แผนก</th>
                <th>ผู้ทำรายการ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTransactions as $transaction)
            <tr>
                <td style="color:#888780;">
                    {{ \Carbon\Carbon::parse($transaction->created_at)->timezone('Asia/Bangkok')->format('d/m/Y H:i') }}
                </td>
                <td>
                    @switch($transaction->transaction_type)
                        @case('in') <span class="badge-in">รับเข้า</span> @break
                        @case('out') <span class="badge-out">จ่ายออก</span> @break
                        @case('adjustment_in') <span class="badge-adj-in">ปรับเพิ่ม</span> @break
                        @case('adjustment_out') <span class="badge-adj-out">ปรับลด</span> @break
                        @default <span>-</span>
                    @endswitch
                </td>
                <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ $transaction->product->name ?? '-' }}
                </td>
                <td style="color:#888780;">{{ $transaction->batch->batch_number ?? '-' }}</td>
                <td style="font-weight:500;color:{{ in_array($transaction->transaction_type, ['out','adjustment_out']) ? '#A32D2D' : '#3B6D11' }};">
                    {{ in_array($transaction->transaction_type, ['out','adjustment_out']) ? '-' : '+' }}{{ number_format($transaction->quantity) }}
                </td>
                <td style="color:#888780;">{{ $transaction->department->name ?? '-' }}</td>
                <td>{{ $transaction->user->fullname ?? $transaction->user->username ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;color:#888780;padding:20px;">ไม่มีรายการเคลื่อนไหว</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Bottom Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:14px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#E6F1FB;"><i class="fas fa-warehouse" style="color:#185FA5;"></i></div>
        <div class="stat-val">{{ number_format($totalStockQuantity) }}</div>
        <div class="stat-label">จำนวนสต็อกทั้งหมด (หน่วย)</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#E6F1FB;"><i class="fas fa-building" style="color:#185FA5;"></i></div>
        <div class="stat-val">{{ number_format($totalDepartments) }}</div>
        <div class="stat-label">แผนกทั้งหมด</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#E6F1FB;"><i class="fas fa-truck" style="color:#185FA5;"></i></div>
        <div class="stat-val">{{ number_format($totalSuppliers) }}</div>
        <div class="stat-label">ผู้จัดจำหน่ายทั้งหมด</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#E6F1FB;"><i class="fas fa-users" style="color:#185FA5;"></i></div>
        <div class="stat-val">{{ number_format($totalUsers) }}</div>
        <div class="stat-label">ผู้ใช้งานทั้งหมด</div>
    </div>
</div>
@endsection