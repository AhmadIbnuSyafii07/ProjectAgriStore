@extends('seller.layout')

@section('title', 'Kelola Stok')

@section('content')
<div class="page-head">
    <div><h1>Kelola Stok</h1><p>Perbarui ketersediaan barang. Stok {{ \App\Models\Product::LOW_STOCK }} atau kurang ditandai kritis.</p></div>
</div>

<form method="GET" class="filter">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama produk">
    <label class="check"><input type="checkbox" name="low" value="1" @checked(request('low')) onchange="this.form.submit()"> Hanya stok kritis</label>
    <button class="btn btn-outline" type="submit"><x-seller.icon name="search" :size="16" /> Cari</button>
</form>

<section class="panel table-panel">
    <div class="table-scroll">
        <table>
            <thead><tr><th>Produk</th><th>Kategori</th><th>Status stok</th><th>Ubah stok</th></tr></thead>
            <tbody>
                @forelse ($products as $p)
                    <tr>
                        <td><strong>{{ $p->product_name }}</strong></td>
                        <td>{{ $p->category?->category_name }}</td>
                        <td>
                            <span class="chip {{ $p->is_low ? 'chip-low' : 'chip-active' }}">{{ $p->is_low ? 'Kritis' : 'Aman' }}</span>
                            <span class="muted">{{ $p->stock }} tersedia</span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('seller.stock.update', $p) }}" class="inline-edit">
                                @csrf @method('PATCH')
                                <input type="number" name="stock" min="0" value="{{ $p->stock }}" aria-label="Stok {{ $p->product_name }}">
                                <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">Tidak ada produk yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('seller.partials.pager', ['paginator' => $products])
</section>
@endsection
