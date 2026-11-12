@extends('layouts.admin')

@section('title', 'Kelola Toko')
@section('breadcrumb', 'Kelola Toko / Verifikasi Vendor')

@section('content')
@php
    $kartu = [
        ['total',    'Total Toko Terdaftar',   'store', 'c-total'],
        ['aktif',    'Toko Aktif Terverifikasi', 'check', 'c-aktif'],
        ['menunggu', 'Toko Menunggu Verifikasi', 'clock', 'c-menunggu'],
        ['ditolak',  'Toko Ditolak/Dibekukan', 'x',     'c-ditolak'],
    ];
@endphp

<div class="toko-page">

    {{-- ================= KOLOM KIRI ================= --}}
    <div class="toko-main">
        <h1 class="page-title">Kelola Toko / Verifikasi Vendor</h1>
        <p class="page-sub">Selamat datang di Panel Manajemen Toko</p>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        {{-- Kartu ringkasan --}}
        <section class="mini-stats">
            @foreach ($kartu as [$key, $label, $icon, $class])
                <div class="mini-stat {{ $class }}">
                    <div>
                        <small>{{ $label }}</small>
                        <strong>{{ $ringkasan[$key] }}</strong>
                    </div>
                    <span class="mini-icon"><x-icon :name="$icon" :size="15" /></span>
                </div>
            @endforeach
        </section>

        {{-- Tabel --}}
        <section class="card">
            <div class="table-head">
                <h2 class="card-title">Daftar Toko Menunggu Verifikasi &amp; Toko Aktif</h2>
                <a href="{{ route('admin.toko') }}#form-toko" class="btn add"><x-icon name="plus" :size="13" /> Tambah Toko</a>
            </div>

            <form method="GET" action="{{ route('admin.toko') }}" class="filters">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search...">
                <select name="status" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach (\App\Models\Toko::STATUS as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>

            <table class="table tbl-toko">
                <thead>
                    <tr>
                        <th>Profil Toko</th>
                        <th>Pemilik &amp; Kontak</th>
                        <th>Wilayah</th>
                        <th>Tanggal Daftar</th>
                        <th>Berkas Legalitas</th>
                        <th>Aksi Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftar as $t)
                        <tr>
                            <td>
                                <strong>{{ $daftar->firstItem() + $loop->index }}. {{ $t->nama_toko }}</strong>
                                <span class="pill s-{{ $t->status }}">{{ $t->status_label }}</span>
                            </td>
                            <td>
                                {{ $t->nama_pemilik }}
                                <span class="sub">{{ $t->email ?: '-' }}</span>
                            </td>
                            <td>{{ $t->wilayah }}</td>
                            <td>{{ $t->created_at->locale('id')->translatedFormat('d M Y') }}</td>
                            <td>{{ $t->berkas ?: '-' }}</td>
                            <td>
                                <div class="act">
                                    {{-- Detail (modal) --}}
                                    <button type="button" class="btn detail" onclick="document.getElementById('detail-{{ $t->id }}').showModal()">
                                        <x-icon name="eye" :size="12" /> Detail
                                    </button>

                                    {{-- Setujui: tampil jika belum aktif --}}
                                    @if ($t->status !== 'aktif')
                                        <form method="POST" action="{{ route('admin.toko.setujui', $t) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn approve"><x-icon name="check" :size="12" /> Setujui</button>
                                        </form>
                                    @endif

                                    {{-- Tolak: tampil jika masih menunggu --}}
                                    @if ($t->status === 'menunggu')
                                        <form method="POST" action="{{ route('admin.toko.tolak', $t) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn tolak"><x-icon name="x" :size="12" /> Tolak</button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.toko', ['edit' => $t->id] + request()->only('q', 'status', 'page')) }}#form-toko" class="btn edit">
                                        <x-icon name="edit" :size="12" /> Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.toko.destroy', $t) }}"
                                          onsubmit="return confirm(@js('Hapus toko '.$t->nama_toko.'? Data tidak bisa dikembalikan.'))">
                                        @csrf @method('DELETE')
                                        <button class="btn delete"><x-icon name="trash" :size="12" /> Hapus</button>
                                    </form>
                                </div>

                                {{-- Modal detail --}}
                                <dialog id="detail-{{ $t->id }}" class="detail-dialog">
                                    <h3>{{ $t->nama_toko }}</h3>
                                    <span class="pill s-{{ $t->status }}">{{ $t->status_label }}</span>
                                    <dl>
                                        <dt>Pemilik</dt><dd>{{ $t->nama_pemilik }}</dd>
                                        <dt>Email</dt><dd>{{ $t->email ?: '-' }}</dd>
                                        <dt>Wilayah</dt><dd>{{ $t->wilayah }}</dd>
                                        <dt>Tanggal Daftar</dt><dd>{{ $t->created_at->locale('id')->translatedFormat('d F Y') }}</dd>
                                        <dt>Berkas Legalitas</dt><dd>{{ $t->berkas ?: '-' }}</dd>
                                    </dl>
                                    <form method="dialog"><button class="btn detail">Tutup</button></form>
                                </dialog>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Belum ada toko yang cocok. Tambahkan lewat form di sebelah kanan.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{ $daftar->links('pagination::bootstrap-4') }}
        </section>
    </div>

    {{-- ================= KOLOM KANAN: FORM ================= --}}
    <aside class="toko-side">
        <div class="card form-card" id="form-toko">
            <h2 class="card-title">{{ $editToko ? 'Edit Data Toko' : 'Tambah / Edit Data Toko' }}</h2>

            <form method="POST" action="{{ $editToko ? route('admin.toko.update', $editToko) : route('admin.toko.store') }}">
                @csrf
                @if ($editToko) @method('PUT') @endif

                <div class="field">
                    <label for="nama_toko">Nama Toko</label>
                    <input id="nama_toko" name="nama_toko" value="{{ old('nama_toko', $editToko?->nama_toko) }}">
                    @error('nama_toko') <p class="err">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label for="nama_pemilik">Nama Pemilik</label>
                    <input id="nama_pemilik" name="nama_pemilik" value="{{ old('nama_pemilik', $editToko?->nama_pemilik) }}">
                    @error('nama_pemilik') <p class="err">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $editToko?->email) }}">
                    @error('email') <p class="err">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label for="wilayah">Wilayah</label>
                    <input id="wilayah" name="wilayah" value="{{ old('wilayah', $editToko?->wilayah) }}">
                    @error('wilayah') <p class="err">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label for="berkas">Berkas Legalitas</label>
                    <input id="berkas" name="berkas" placeholder="KTP, NIB (Lengkap)" value="{{ old('berkas', $editToko?->berkas) }}">
                    @error('berkas') <p class="err">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        @foreach (\App\Models\Toko::STATUS as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', $editToko?->status ?? 'menunggu') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="err">{{ $message }}</p> @enderror
                </div>

                <button class="btn save">Simpan</button>
                @if ($editToko)
                    <a href="{{ route('admin.toko') }}" class="cancel">Batal edit</a>
                @endif
            </form>
        </div>
    </aside>
</div>
@endsection
