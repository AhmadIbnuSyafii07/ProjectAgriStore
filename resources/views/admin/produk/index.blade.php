@extends('layouts.layouts_admin.admin')

@section('title', 'Kelola Produk')
@section('breadcrumb', 'Dashboard Super Admin / Kelola Produk')

@section('content')
@php
    $kartu = [
        ['total',   'Total Produk',     'ps-total'],
        ['aktif',   'Produk Aktif',     'ps-aktif'],
        ['menipis', 'Stok Menipis',     'ps-menipis'],
        ['draft',   'Draft / Nonaktif', 'ps-draft'],
    ];
@endphp

{{-- ============ 4 KARTU RINGKASAN (nilai 0 dulu) ============ --}}
<section class="p-stats">
    @foreach ($kartu as [$key, $label, $class])
        <div class="p-stat {{ $class }}">
            <div class="p-stat-top">
                <span>{{ $label }}</span>
                <span class="p-icon"><x-admin.icon name="trend" :size="14" /></span>
            </div>
            <strong>{{ number_format($ringkasan[$key], 0, ',', '.') }}</strong>
            <svg class="p-spark" viewBox="0 0 100 30" preserveAspectRatio="none">
                <path d="M0,26 L100,26" fill="none" stroke="rgba(255,255,255,.75)" stroke-width="2" vector-effect="non-scaling-stroke"/>
            </svg>
        </div>
    @endforeach
</section>

{{-- ============ DAFTAR PRODUK ============ --}}
<section class="card">
    <div class="p-toolbar">
        <h2 class="p-title">Daftar Produk Perkebunan</h2>

        <div class="p-tools">
            <form method="GET" action="{{ route('admin.produk') }}" class="p-tools-form">
                <div class="p-search">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Pencarian...">
                    <x-admin.icon name="search" :size="14" />
                </div>
                <select name="kategori" class="p-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategori as $k)
                        <option value="{{ $k }}" @selected(request('kategori') === $k)>{{ $k }}</option>
                    @endforeach
                </select>
            </form>
            <button type="button" class="btn add lg"><x-admin.icon name="plus" :size="14" /> Tambah Produk</button>
        </div>
    </div>

    <table class="table tbl-produk">
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Detail Produk</th>
                <th>Kategori</th>
                <th>Penjual/Vendor</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produk as $p)
                {{-- Template baris. Sesuaikan nama field dengan tabel produk dari penjual. --}}
                <tr>
                    <td><img class="thumb" src="{{ $p->gambar_url }}" alt=""></td>
                    <td>
                        <strong>{{ $p->nama_produk }}</strong>
                        <span class="sub">Kode: {{ $p->kode }}</span>
                    </td>
                    <td>{{ $p->kategori }}</td>
                    <td>{{ $p->vendor }}</td>
                    <td>Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                    <td>
                        @if ($p->stok <= 0)
                            <span class="stok out">0 {{ $p->satuan }}</span>
                        @elseif ($p->stok <= 15)
                            <span class="stok low">{{ $p->stok }} {{ $p->satuan }}</span>
                        @else
                            <span class="stok ok">{{ $p->stok }} {{ $p->satuan }}</span>
                        @endif
                    </td>
                    <td><span class="pill s-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    <td class="center">
                        <button type="button" class="aksi-link"><x-admin.icon name="edit" :size="13" /> Edit</button>
                        <button type="button" class="aksi-link"><x-admin.icon name="trash" :size="13" /> Hapus</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty">
                        Belum ada produk. Daftar ini akan terisi otomatis dari produk yang ditambahkan penjual.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-foot">
        <span>Menampilkan {{ $produk->count() }} dari {{ number_format($ringkasan['total'], 0, ',', '.') }} produk</span>
    </div>
</section>
@endsection