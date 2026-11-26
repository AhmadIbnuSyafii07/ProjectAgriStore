@extends('seller.layout')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="page-head">
    <div>
        <h1>Laporan Penjualan</h1>
        <p>Periode {{ $from->format('d/m/Y') }} sampai {{ $to->format('d/m/Y') }} (hanya pembayaran Valid).</p>
    </div>
    <div class="actions no-print">
        <a class="btn btn-outline" href="{{ route('seller.reports.index', ['from' => $from->toDateString(), 'to' => $to->toDateString(), 'export' => 'csv']) }}">
            <x-seller.icon name="download" :size="16" /> Unduh Excel (CSV)
        </a>
        <button type="button" class="btn btn-outline" onclick="window.print()"><x-seller.icon name="print" :size="16" /> Cetak / simpan PDF</button>
    </div>
</div>

<form method="GET" class="filter no-print">
    <label>Dari <input type="date" name="from" value="{{ $from->toDateString() }}"></label>
    <label>Sampai <input type="date" name="to" value="{{ $to->toDateString() }}"></label>
    <button class="btn btn-primary" type="submit">Tampilkan</button>
</form>

<section class="stats" style="grid-template-columns:repeat(3,1fr)">
    <article class="stat"><span class="stat-ico"><x-seller.icon name="wallet" :size="20" /></span>
        <div><small>Total pendapatan</small><strong>Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</strong></div></article>
    <article class="stat"><span class="stat-ico"><x-seller.icon name="cart" :size="20" /></span>
        <div><small>Jumlah pesanan</small><strong>{{ $summary['orders'] }}</strong></div></article>
    <article class="stat"><span class="stat-ico"><x-seller.icon name="box" :size="20" /></span>
        <div><small>Barang terjual</small><strong>{{ $summary['items'] }}</strong></div></article>
</section>

<section class="grid-2">
    <article class="panel table-panel">
        <div class="table-scroll">
            <table>
                <thead><tr><th>Code</th><th>Tanggal</th><th>Buyer</th><th>Product</th><th class="right">Total</th></tr></thead>
                <tbody>
                    @forelse ($orders as $o)
                        <tr>
                            <td class="mono">#{{ $o->order_number }}</td>
                            <td>{{ $o->created_at->format('d/m/Y') }}</td>
                            <td>{{ $o->buyer?->name }}</td>
                            <td>{{ $o->product_summary }}</td>
                            <td class="right">Rp {{ number_format($o->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">Tidak ada penjualan pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    <article class="panel">
        <h2>Produk terlaris</h2>
        @forelse ($top as $name => $qty)
            <div class="stock-top"><span>{{ $name }}</span><b style="color:var(--navy)">{{ $qty }} terjual</b></div>
        @empty
            <p class="muted">Belum ada data.</p>
        @endforelse
    </article>
</section>
@endsection
