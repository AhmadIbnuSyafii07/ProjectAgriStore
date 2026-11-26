@extends('seller.layout')

@section('title', 'Pesanan')

@section('content')
<div class="page-head">
    <div><h1>Pesanan</h1><p>Pantau dan proses pesanan dari pembeli.</p></div>
</div>

<form method="GET" class="filter">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no. pesanan atau pembeli">
    <select name="status">
        <option value="">Semua status</option>
        <option value="new" @selected(request('status') === 'new')>Perlu diproses</option>
        <option value="shipped" @selected(request('status') === 'shipped')>Dikirim</option>
        <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
        <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
    </select>
    <button class="btn btn-outline" type="submit"><x-seller.icon name="search" :size="16" /> Cari</button>
</form>

<section class="panel table-panel">
    <div class="table-scroll">
        <table>
            <thead>
                <tr><th>Code</th><th>Tanggal</th><th>Buyer</th><th>Product</th><th>Total</th><th>Pembayaran</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse ($orders as $o)
                    <tr>
                        <td class="mono">#{{ $o->order_number }}</td>
                        <td>{{ $o->created_at->format('d/m/Y') }}</td>
                        <td>{{ $o->buyer?->name }}</td>
                        <td>{{ $o->product_summary }}</td>
                        <td>Rp {{ number_format($o->grand_total, 0, ',', '.') }}</td>
                        <td><span class="chip chip-{{ $o->payment_status }}">{{ $o->payment_label }}</span></td>
                        <td><span class="chip chip-{{ $o->order_status }}">{{ $o->order_label }}</span></td>
                        <td><a href="{{ route('seller.orders.show', $o) }}" class="btn btn-sm btn-outline"><x-seller.icon name="eye" :size="14" /> Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">Tidak ada pesanan yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('seller.partials.pager', ['paginator' => $orders])
</section>
@endsection
