@extends('seller.layout')

@section('title', 'Pesanan ' . $order->order_number)

@section('content')
<div class="page-head">
    <div>
        <h1>Pesanan #{{ $order->order_number }}</h1>
        <p>Dibuat {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <div class="actions no-print">
        <a href="{{ route('seller.orders.index') }}" class="btn btn-outline">Kembali</a>
        <button type="button" class="btn btn-outline" onclick="window.print()"><x-seller.icon name="print" :size="16" /> Cetak label</button>
    </div>
</div>

<section class="grid-eq">
    <article class="panel">
        <h2>Info pesanan</h2>
        <dl class="kv">
            <dt>Pembeli</dt><dd>{{ $order->buyer?->name }}</dd>
            <dt>Email</dt><dd>{{ $order->buyer?->email }}</dd>
            <dt>Status pesanan</dt><dd><span class="chip chip-{{ $order->order_status }}">{{ $order->order_label }}</span></dd>
            <dt>Status bayar</dt><dd><span class="chip chip-{{ $order->payment_status }}">{{ $order->payment_label }}</span></dd>
            <dt>No. resi</dt><dd>{{ $order->tracking_number ?? '-' }}</dd>
            <dt>Total tagihan</dt><dd>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</dd>
        </dl>
    </article>

    <article class="panel">
        <h2>Pembayaran</h2>
        <dl class="kv">
            <dt>Metode</dt><dd>{{ $order->payment?->payment_method ?? '-' }}</dd>
            <dt>Tanggal valid</dt><dd>{{ $order->payment?->payment_date?->format('d/m/Y H:i') ?? '-' }}</dd>
            <dt>Bukti transfer</dt>
            <dd>
                @if ($order->payment?->proof_url)
                    <a href="{{ $order->payment->proof_url }}" target="_blank"><img class="proof" src="{{ $order->payment->proof_url }}" alt="Bukti transfer"></a>
                @else
                    <span class="muted">Belum ada file bukti.</span>
                @endif
            </dd>
        </dl>

        <div class="actions no-print" style="margin-top:16px">
            @if ($order->payment_status === 'waiting')
                <form method="POST" action="{{ route('seller.orders.payment', $order) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="valid">
                    <button class="btn btn-ok" type="submit"><x-seller.icon name="check" :size="16" /> Valid</button>
                </form>
                <form method="POST" action="{{ route('seller.orders.payment', $order) }}" onsubmit="return confirm('Tolak pembayaran ini?')">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="reject">
                    <button class="btn btn-danger" type="submit">Tolak</button>
                </form>
            @endif
        </div>
    </article>
</section>

<section class="panel table-panel">
    <div class="table-scroll">
        <table>
            <thead><tr><th>Produk</th><th>Harga satuan</th><th>Qty</th><th class="right">Subtotal</th></tr></thead>
            <tbody>
                @foreach ($order->items as $i)
                    <tr>
                        <td>{{ $i->product?->product_name ?? '-' }}</td>
                        <td>Rp {{ number_format($i->price_at_purchase, 0, ',', '.') }}</td>
                        <td>{{ $i->quantity }}</td>
                        <td class="right">Rp {{ number_format($i->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="right"><strong>Total (termasuk ongkir)</strong></td>
                    <td class="right"><strong>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

@if ($order->payment_status === 'paid' && $order->order_status === 'processing')
    <section class="panel no-print">
        <h2>Kirim pesanan</h2>
        <form method="POST" action="{{ route('seller.orders.ship', $order) }}" class="inline-edit">
            @csrf @method('PATCH')
            <input type="text" name="tracking_number" placeholder="Nomor resi ekspedisi" required style="width:260px">
            <button class="btn btn-primary" type="submit"><x-seller.icon name="truck" :size="16" /> Simpan resi &amp; kirim</button>
        </form>
    </section>
@endif
@endsection
