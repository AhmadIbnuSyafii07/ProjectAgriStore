@extends('layouts.layouts_admin.admin')

@section('title', 'Kelola Pengguna')
@section('breadcrumb', 'Dashboard Super Admin / Kelola Pengguna')

@section('content')
@php
    $kartu = [
        ['total',        'Total Pengguna',     'Pembeli & Vendor',  'us-total'],
        ['aktif',        'Pengguna Aktif',     'Aktivitas Hari Ini', 'us-aktif'],
        ['baru',         'Akun Baru',          '7 Hari Terakhir',   'us-baru'],
        ['ditangguhkan', 'Akun Ditangguhkan',  'Butuh Peninjauan',  'us-tahan'],
    ];
@endphp

{{-- ============ 4 KARTU RINGKASAN (nilai 0 dulu) ============ --}}
<section class="p-stats">
    @foreach ($kartu as [$key, $label, $catatan, $class])
        <div class="p-stat {{ $class }}">
            <div class="p-stat-top">
                <span>{{ $label }}</span>
                <span class="p-icon"><x-admin.icon name="trend" :size="14" /></span>
            </div>
            <strong>{{ number_format($ringkasan[$key], 0, ',', '.') }}</strong>
            <span class="p-sub">{{ $catatan }}</span>
            <svg class="p-spark" viewBox="0 0 100 30" preserveAspectRatio="none">
                <path d="M0,26 L100,26" fill="none" stroke="rgba(255,255,255,.75)" stroke-width="2" vector-effect="non-scaling-stroke"/>
            </svg>
        </div>
    @endforeach
</section>

{{-- ============ DAFTAR PENGGUNA ============ --}}
<section class="card">
    <div class="p-toolbar">
        <h2 class="p-title">Daftar Pengguna Platform</h2>

        <div class="p-tools">
            <form method="GET" action="{{ route('admin.pengguna') }}" class="p-tools-form">
                <div class="p-search">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau peran...">
                    <x-admin.icon name="search" :size="14" />
                </div>
                <select name="peran" class="p-select" onchange="this.form.submit()">
                    <option value="">Semua Peran</option>
                    @foreach ($peran as $r)
                        <option value="{{ $r }}" @selected(request('peran') === $r)>{{ $r }}</option>
                    @endforeach
                </select>
            </form>
            <button type="button" class="btn add lg"><x-admin.icon name="plus" :size="14" /> Tambah Pengguna</button>
        </div>
    </div>

    <table class="table tbl-produk tbl-pengguna">
        <thead>
            <tr>
                <th>Avatar &amp; Nama</th>
                <th>Email</th>
                <th>Peran</th>
                <th>Tanggal Bergabung</th>
                <th>Status</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengguna as $u)
                {{-- Template baris. Sesuaikan nama field dengan tabel pengguna/role yang dipakai. --}}
                <tr>
                    <td>
                        <div class="u-user">
                            @if ($u->foto_url)
                                <img class="avatar-sm" src="{{ $u->foto_url }}" alt="">
                            @else
                                <span class="avatar-sm initial">{{ strtoupper(mb_substr($u->nama, 0, 1)) }}</span>
                            @endif
                            <div>
                                <strong>{{ $u->nama }}</strong>
                                @if ($u->nama_toko)<span class="sub">{{ $u->nama_toko }}</span>@endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $u->email }}</td>
                    <td>
                        {{ $u->peran }}
                        @if ($u->nama_toko)<span class="sub dark">{{ $u->nama_toko }}</span>@endif
                    </td>
                    <td>{{ $u->created_at->locale('id')->translatedFormat('d M Y') }}</td>
                    <td><span class="pill s-{{ $u->status }}">{{ ucfirst($u->status) }}</span></td>
                    <td class="center">
                        <div class="ab-group">
                            <button type="button" class="ab"><x-admin.icon name="edit" :size="12" /> Edit</button>
                            @if ($u->status === 'ditangguhkan')
                                <button type="button" class="ab green"><x-admin.icon name="unlock" :size="12" /> Aktifkan</button>
                            @else
                                <button type="button" class="ab"><x-admin.icon name="lock" :size="12" /> Suspend</button>
                            @endif
                            <button type="button" class="ab red"><x-admin.icon name="trash" :size="12" /> Hapus</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty">
                        Belum ada pengguna. Daftar ini akan terisi otomatis dari data akun pembeli, penjual/vendor, dan super admin.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-foot">
        <span>Menampilkan {{ $pengguna->count() }} dari {{ number_format($ringkasan['total'], 0, ',', '.') }} pengguna</span>
    </div>
</section>
@endsection