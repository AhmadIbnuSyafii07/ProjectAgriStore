@extends('seller.layout')

@section('title', 'Produk Saya')

@section('content')
<div class="page-head">
    <div>
        <h1>Produk Saya</h1>
        <p>Kelola katalog produk toko Anda.</p>
    </div>
    <a href="{{ route('seller.products.create') }}" class="btn btn-primary"><x-seller.icon name="plus" :size="16" /> Tambah Produk</a>
</div>

<form method="GET" class="filter">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama produk">
    <select name="category">
        <option value="">Semua kategori</option>
        @foreach ($categories as $c)
            <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->category_name }}</option>
        @endforeach
    </select>
    <button class="btn btn-outline" type="submit"><x-seller.icon name="search" :size="16" /> Cari</button>
</form>

<section class="panel table-panel">
    <div class="table-scroll">
        <table>
            <thead>
                <tr><th>Foto</th><th>Produk</th><th>Harga</th><th>Stok</th><th>Berat</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                    <tr>
                        <td>
                            @if ($p->image_url)
                                <img class="thumb" src="{{ $p->image_url }}" alt="{{ $p->product_name }}">
                            @else
                                <span class="thumb">Foto</span>
                            @endif
                        </td>
                        <td><strong>{{ $p->product_name }}</strong><br><span class="muted">{{ $p->category?->category_name }}</span></td>
                        <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td>
                            {{ $p->stock }}
                            @if ($p->is_low)<span class="chip chip-low">Kritis</span>@endif
                        </td>
                        <td>{{ number_format($p->weight_gram, 0, ',', '.') }} g</td>
                        <td>
                            <span class="chip {{ $p->is_active ? 'chip-active' : 'chip-inactive' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('seller.products.edit', $p) }}" class="btn btn-sm btn-outline"><x-seller.icon name="edit" :size="14" /> Edit</a>

                                <form method="POST" action="{{ route('seller.products.toggle', $p) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline" type="submit">{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                </form>

                                <form method="POST" action="{{ route('seller.products.destroy', $p) }}"
                                      onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit"><x-seller.icon name="trash" :size="14" /></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada produk. Klik "Tambah Produk" untuk mulai berjualan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('seller.partials.pager', ['paginator' => $products])
</section>
@endsection
