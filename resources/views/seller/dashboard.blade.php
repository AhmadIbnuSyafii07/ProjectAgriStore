@extends('seller.layout')

@section('title', 'Dashboard')

@section('content')
@php
    $weeklyMax  = max(max($weekly), 1);
    $monthlyMax = max(max($monthly), 1);

    $total = array_sum($status);
    $pct   = fn ($n) => $total > 0 ? round($n / $total * 100) : 0;
    $p     = $pct($status['processed']);
    $s     = $p + $pct($status['shipped']);
    $donut = $total > 0
        ? "conic-gradient(#1B2A4A 0 {$p}%, #2F8F83 {$p}% {$s}%, #E8590C {$s}% 100%)"
        : 'conic-gradient(#DCE1EB 0 100%)';
    $legend = ['processed' => 'Diproses', 'shipped' => 'Dikirim', 'pending' => 'Menunggu'];
@endphp

<div class="page-head">
    <div>
        <h1>Halo, {{ auth()->user()->name }}</h1>
        <p>
            @if ($stats['new_orders'] > 0)
                Ada {{ $stats['new_orders'] }} pesanan yang perlu Anda proses hari ini.
            @else
                Belum ada pesanan yang perlu diproses.
            @endif
        </p>
    </div>
</div>

<section class="stats">
    <article class="stat">
        <span class="stat-ico"><x-seller.icon name="box" :size="20" /></span>
        <div><small>New Orders</small><strong>{{ $stats['new_orders'] }}</strong></div>
    </article>
    <article class="stat">
        <span class="stat-ico"><x-seller.icon name="check" :size="20" /></span>
        <div><small>Active Products</small><strong>{{ $stats['active_products'] }}</strong></div>
    </article>
    <article class="stat stat-warn">
        <span class="stat-ico"><x-seller.icon name="alert" :size="20" /></span>
        <div><small>Low Stock</small><strong>{{ $stats['low_stock'] }}</strong></div>
    </article>
    <article class="stat">
        <span class="stat-ico"><x-seller.icon name="wallet" :size="20" /></span>
        <div><small>This Month's Revenue</small><strong>Rp {{ number_format($stats['month_revenue'], 0, ',', '.') }}</strong></div>
    </article>
</section>

<section class="grid-2">
    <article class="panel">
        <h2>Penjualan 7 Hari (jumlah pesanan)</h2>
        <div class="bars bars-lg">
            @foreach ($weekly as $day => $val)
                <div class="bar-col" title="{{ $val }} pesanan">
                    <div class="bar" style="height: {{ round($val / $weeklyMax * 100) }}%"></div>
                    <span>{{ $day }}</span>
                </div>
            @endforeach
        </div>
    </article>

    <article class="panel">
        <h2>Status Pesanan</h2>
        <div class="donut-wrap">
            <div class="donut" style="background: {{ $donut }}">
                <div class="donut-hole"><strong>{{ $total }}</strong><span>pesanan</span></div>
            </div>
            <ul class="legend">
                @foreach ($legend as $key => $label)
                    <li><i class="sw sw-{{ $key }}"></i>{{ $label }} {{ $pct($status[$key]) }}%</li>
                @endforeach
            </ul>
        </div>
    </article>
</section>

<section class="panel table-panel">
    <div class="table-scroll">
        <table>
            <thead>
                <tr><th>Code</th><th>Buyer</th><th>Product</th><th>Total</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse ($orders as $o)
                    <tr>
                        <td class="mono">#{{ $o->order_number }}</td>
                        <td>{{ $o->buyer?->name }}</td>
                        <td>{{ $o->product_summary }}</td>
                        <td>Rp {{ number_format($o->grand_total, 0, ',', '.') }}</td>
                        <td><span class="chip chip-{{ $o->order_status }}">{{ $o->order_label }}</span></td>
                        <td><a href="{{ route('seller.orders.show', $o) }}" class="btn btn-sm btn-outline">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada pesanan. Pesanan baru akan muncul di sini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="grid-2">
    <article class="panel">
        <h2>Manage Stock</h2>
        @if ($lowStockItems->isEmpty())
            <p class="muted">Semua stok aman.</p>
        @else
            <ul class="stock-list">
                @foreach ($lowStockItems as $item)
                    <li>
                        <div class="stock-top"><span>{{ $item->product_name }}</span><b>{{ $item->stock }} tersisa</b></div>
                        <div class="meter"><i style="width: {{ min(100, round($item->stock / \App\Models\Product::LOW_STOCK * 100)) }}%"></i></div>
                    </li>
                @endforeach
            </ul>
        @endif
        <a href="{{ route('seller.stock.index') }}" class="link">Kelola semua stok</a>
    </article>

    <article class="panel">
        <h2>Monthly Charts (juta rupiah)</h2>
        <div class="bars bars-sm">
            @foreach ($monthly as $m => $val)
                <div class="bar-col" title="Rp {{ $val }} juta">
                    <div class="bar" style="height: {{ round($val / $monthlyMax * 100) }}%"></div>
                    <span>{{ $m }}</span>
                </div>
            @endforeach
        </div>
    </article>
</section>
@endsection
