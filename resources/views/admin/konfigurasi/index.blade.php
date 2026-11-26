@extends('layouts.layouts_admin.admin')

@section('title', 'Konfigurasi Sistem')
@section('breadcrumb', 'Dashboard Super Admin / Konfigurasi Sistem')

@section('content')
@php
    // Saklar: pakai isian lama kalau validasi gagal, kalau tidak pakai nilai dari database
    $aktif = fn ($k) => count(old()) ? (bool) old($k) : (($p[$k] ?? '0') === '1');
    $nilai = fn ($k) => old($k, $p[$k] ?? '');

    // Kalau ada error validasi, buka tab yang berisi field bermasalah
    $tabError = null;
    $petaTab = [
        'umum'      => ['nama_platform', 'email_dukungan', 'telepon', 'alamat'],
        'transaksi' => ['min_transaksi', 'biaya_penanganan', 'batas_konfirmasi'],
    ];
    foreach ($petaTab as $tab => $fields) {
        foreach ($fields as $f) {
            if ($errors->has($f)) { $tabError = $tab; break 2; }
        }
    }

    $maint = $aktif('mode_pemeliharaan');
    $jumlahAman = collect(['aman_captcha', 'aman_ip', 'aman_backup'])->filter(fn ($k) => $aktif($k))->count();
@endphp

@if (session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert danger">Ada isian yang belum benar. Periksa kolom yang ditandai merah.</div>
@endif

<form id="konfig-form" method="POST" action="{{ route('admin.konfigurasi.update') }}">
    @csrf
    @method('PUT')

    {{-- ============ 4 KARTU STATUS (hidup: ikut berubah saat saklar digeser) ============ --}}
    <section class="p-stats">
        <div class="p-stat kc kc-sys {{ $maint ? 'is-maint' : '' }}" id="card-sys">
            <div class="p-stat-top">
                <span>Status Sistem</span>
                <span class="p-icon"><x-admin.icon name="radio" :size="15" /></span>
            </div>
            <strong><i class="dot"></i><span id="sys-value">{{ $maint ? 'Pemeliharaan' : 'Aktif / Online' }}</span></strong>
        </div>

        <div class="p-stat kc kc-ver">
            <div class="p-stat-top">
                <span>Versi Platform</span>
                <span class="p-icon"><x-admin.icon name="refresh" :size="15" /></span>
            </div>
            <strong>{{ $versi }}</strong>
        </div>

        <div class="p-stat kc kc-sec">
            <div class="p-stat-top">
                <span>Keamanan</span>
                <span class="p-icon"><x-admin.icon name="shield" :size="15" /></span>
            </div>
            <strong id="sec-value">{{ $jumlahAman }} / 3 Proteksi Aktif</strong>
        </div>

        <div class="p-stat kc kc-mnt">
            <div class="p-stat-top">
                <span>Mode Pemeliharaan</span>
                <label class="switch">
                    <input type="checkbox" name="mode_pemeliharaan" value="1" @checked($maint)>
                    <span class="slider"></span>
                </label>
            </div>
            <strong id="mnt-value">{{ $maint ? 'Aktif' : 'Nonaktif' }}</strong>
        </div>
    </section>

    {{-- ============ TAB ============ --}}
    <nav class="k-tabs" role="tablist">
        <button type="button" class="k-tab active" data-tab="umum"><x-admin.icon name="globe" :size="15" /> Umum</button>
        <button type="button" class="k-tab" data-tab="transaksi"><x-admin.icon name="repeat" :size="15" /> Transaksi</button>
        <button type="button" class="k-tab" data-tab="pembayaran"><x-admin.icon name="card" :size="15" /> Pembayaran</button>
        <button type="button" class="k-tab" data-tab="keamanan"><x-admin.icon name="shield" :size="15" /> Fitur &amp; Keamanan</button>
        <button type="button" class="k-tab" data-tab="master"><x-admin.icon name="layers" :size="15" /> Master Data</button>
    </nav>

    {{-- ---------- TAB: UMUM ---------- --}}
    <section class="k-panel active" data-panel="umum">
        <div class="k-split">
            <div class="k-card">
                <h2 class="k-h">Identitas Platform</h2>
                <p class="k-sub">Nama dan kontak yang tampil ke pembeli dan penjual.</p>
                <div class="k-fields">
                    <div class="field">
                        <label for="nama_platform">Nama Platform</label>
                        <input id="nama_platform" name="nama_platform" value="{{ $nilai('nama_platform') }}">
                        @error('nama_platform') <p class="err">{{ $message }}</p> @enderror
                    </div>
                    <div class="field">
                        <label for="email_dukungan">Email Dukungan</label>
                        <input id="email_dukungan" type="email" name="email_dukungan" value="{{ $nilai('email_dukungan') }}">
                        @error('email_dukungan') <p class="err">{{ $message }}</p> @enderror
                    </div>
                    <div class="field">
                        <label for="telepon">Nomor Telepon</label>
                        <input id="telepon" name="telepon" value="{{ $nilai('telepon') }}" placeholder="08xx-xxxx-xxxx">
                        @error('telepon') <p class="err">{{ $message }}</p> @enderror
                    </div>
                    <div class="field full">
                        <label for="alamat">Alamat Kantor</label>
                        <textarea id="alamat" name="alamat" rows="3" placeholder="Jl. ...">{{ $nilai('alamat') }}</textarea>
                        @error('alamat') <p class="err">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="k-card">
                <h2 class="k-h">Logo &amp; Favicon</h2>
                <p class="k-sub">Gambar persegi, minimal 256 × 256 px.</p>
                <div class="k-drop">
                    <div class="k-preview" id="logo-preview"><x-admin.icon name="image" :size="28" /></div>
                    <div>
                        <label class="k-btn dark" for="logo-input"><x-admin.icon name="upload" :size="14" /> Unggah Baru</label>
                        <input id="logo-input" type="file" accept="image/*" hidden>
                        <p class="k-hint">Saat ini hanya pratinjau. Penyimpanan file logo diaktifkan nanti.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ---------- TAB: TRANSAKSI ---------- --}}
    <section class="k-panel" data-panel="transaksi">
        <div class="k-card">
            <h2 class="k-h">Pengaturan Transaksi</h2>
            <p class="k-sub">Aturan yang berlaku untuk semua pesanan di platform.</p>
            <div class="k-fields three">
                <div class="field">
                    <label for="min_transaksi">Minimal Transaksi</label>
                    <div class="k-affix pre">
                        <em>Rp</em>
                        <input id="min_transaksi" type="number" min="0" step="1" name="min_transaksi" value="{{ $nilai('min_transaksi') }}">
                    </div>
                    @error('min_transaksi') <p class="err">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                    <label for="biaya_penanganan">Default Biaya Penanganan</label>
                    <div class="k-affix suf">
                        <input id="biaya_penanganan" type="number" min="0" max="100" step="0.1" name="biaya_penanganan" value="{{ $nilai('biaya_penanganan') }}">
                        <em>%</em>
                    </div>
                    @error('biaya_penanganan') <p class="err">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                    <label for="batas_konfirmasi">Batas Waktu Konfirmasi Pembayaran</label>
                    <div class="k-affix suf">
                        <input id="batas_konfirmasi" type="number" min="1" max="720" step="1" name="batas_konfirmasi" value="{{ $nilai('batas_konfirmasi') }}">
                        <em>jam</em>
                    </div>
                    @error('batas_konfirmasi') <p class="err">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </section>

    {{-- ---------- TAB: PEMBAYARAN ---------- --}}
    <section class="k-panel" data-panel="pembayaran">
        <div class="k-card">
            <h2 class="k-h">Metode Pembayaran &amp; Gateway</h2>
            <p class="k-sub">Aktifkan metode yang boleh dipakai pembeli saat checkout.</p>

            @foreach ([
                ['bayar_va',       'Virtual Account (Pembayaran Otomatis)', 'Pembeli membayar lewat nomor VA bank, terverifikasi otomatis.', 'card',   'green'],
                ['bayar_ewallet',  'E-Wallet',                              'Pembayaran lewat dompet digital seperti GoPay, OVO, dan DANA.',   'wallet', 'violet'],
                ['bayar_transfer', 'Transfer Bank Manual',                  'Pembeli mengunggah bukti transfer, lalu diverifikasi manual.',    'bank',   'amber'],
            ] as [$kunci, $judul, $ket, $ikon, $warna])
                <div class="k-row">
                    <span class="k-ico {{ $warna }}"><x-admin.icon :name="$ikon" :size="18" /></span>
                    <div class="k-txt"><strong>{{ $judul }}</strong><span>{{ $ket }}</span></div>
                    <button type="button" class="k-btn ghost" data-soon="Konfigurasi API aktif setelah modul pembayaran terhubung.">Konfigurasi API</button>
                    <label class="switch">
                        <input type="checkbox" name="{{ $kunci }}" value="1" @checked($aktif($kunci))>
                        <span class="slider"></span>
                    </label>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ---------- TAB: FITUR & KEAMANAN ---------- --}}
    <section class="k-panel" data-panel="keamanan">
        <div class="k-split even">
            <div class="k-card">
                <h2 class="k-h">Fitur Unggulan &amp; Layanan</h2>
                <p class="k-sub">Fitur tambahan yang tersedia untuk pengguna.</p>
                @foreach ([
                    ['fitur_kalkulator',  'Kalkulator Dosis Pupuk',  'Hitung kebutuhan pupuk per hektare untuk pembeli.', 'calc',  'green'],
                    ['fitur_stok_kritis', 'Peringatan Stok Kritis',  'Kirim notifikasi ke penjual saat stok menipis.',    'alert', 'amber'],
                ] as [$kunci, $judul, $ket, $ikon, $warna])
                    <div class="k-row">
                        <span class="k-ico {{ $warna }}"><x-admin.icon :name="$ikon" :size="18" /></span>
                        <div class="k-txt"><strong>{{ $judul }}</strong><span>{{ $ket }}</span></div>
                        <label class="switch">
                            <input type="checkbox" name="{{ $kunci }}" value="1" @checked($aktif($kunci))>
                            <span class="slider"></span>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="k-card">
                <h2 class="k-h">Keamanan &amp; Server</h2>
                <p class="k-sub">Perlindungan akses dan cadangan data.</p>
                @foreach ([
                    ['aman_captcha', 'Gunakan Captcha saat Login',   'Mengurangi percobaan login otomatis oleh bot.', 'shield',   'blue'],
                    ['aman_ip',      'Pembatasan Akses IP',          'Batasi panel admin hanya dari IP tertentu.',    'server',   'violet'],
                    ['aman_backup',  'Backup Database Otomatis',     'Cadangan data terjadwal setiap hari.',          'database', 'green'],
                ] as [$kunci, $judul, $ket, $ikon, $warna])
                    <div class="k-row">
                        <span class="k-ico {{ $warna }}"><x-admin.icon :name="$ikon" :size="18" /></span>
                        <div class="k-txt"><strong>{{ $judul }}</strong><span>{{ $ket }}</span></div>
                        <label class="switch">
                            <input type="checkbox" name="{{ $kunci }}" value="1" @checked($aktif($kunci))>
                            <span class="slider"></span>
                        </label>
                    </div>
                @endforeach
                <button type="button" class="k-btn primary mt" data-soon="Backup manual diaktifkan nanti."><x-admin.icon name="database" :size="14" /> Backup Sekarang</button>
            </div>
        </div>
    </section>

    {{-- ---------- TAB: MASTER DATA (tempat CRUD, dikosongkan dulu) ---------- --}}
    <section class="k-panel" data-panel="master">
        <div class="k-split even">
            @foreach ([
                ['Kategori Produk', 'Dipakai penjual saat menambah produk.',                'tag',    'blue',   $kategori],
                ['Satuan Produk',   'Contoh: Sak, Pcs, Pasang, Liter. Tampil di kolom stok.', 'layers', 'violet', $satuan],
            ] as [$judul, $ket, $ikon, $warna, $data])
                <div class="k-card">
                    <div class="k-card-head">
                        <span class="k-ico {{ $warna }}"><x-admin.icon :name="$ikon" :size="18" /></span>
                        <div class="k-txt"><strong>{{ $judul }}</strong><span>{{ $ket }}</span></div>
                        <button type="button" class="k-btn primary sm" data-soon="CRUD {{ $judul }} dibuat setelah modul penjual terhubung."><x-admin.icon name="plus" :size="13" /> Tambah</button>
                    </div>

                    <table class="table compact-md">
                        <thead>
                            <tr><th>Nama</th><th>Dipakai</th><th class="center">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $row)
                                {{-- Template baris. Sesuaikan field dengan tabelnya. --}}
                                <tr>
                                    <td>{{ $row->nama }}</td>
                                    <td>{{ $row->produk_count ?? 0 }} produk</td>
                                    <td class="center">
                                        <div class="ab-group">
                                            <button type="button" class="ab"><x-admin.icon name="edit" :size="12" /> Edit</button>
                                            <button type="button" class="ab red"><x-admin.icon name="trash" :size="12" /> Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="empty">Belum ada data. Akan terisi saat CRUD {{ strtolower($judul) }} aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Bar simpan melayang: muncul saat ada perubahan --}}
    <div class="k-savebar" id="savebar">
        <span>Ada perubahan yang belum disimpan</span>
        <button type="reset" class="k-btn ghost-dark">Reset</button>
        <button type="submit" class="k-btn primary"><x-admin.icon name="save" :size="14" /> Simpan Perubahan</button>
    </div>
</form>

<div class="k-toast" id="toast"></div>

<script>
(function () {
    var form = document.getElementById('konfig-form');
    var bar = document.getElementById('savebar');
    var toastEl = document.getElementById('toast');
    var timer;

    /* ---- Tab ---- */
    var tabs = [].slice.call(document.querySelectorAll('[data-tab]'));
    var panels = [].slice.call(document.querySelectorAll('[data-panel]'));
    function show(name) {
        tabs.forEach(function (t) { t.classList.toggle('active', t.dataset.tab === name); });
        panels.forEach(function (p) { p.classList.toggle('active', p.dataset.panel === name); });
        try { localStorage.setItem('konfig-tab', name); } catch (e) {}
    }
    tabs.forEach(function (t) { t.addEventListener('click', function () { show(t.dataset.tab); }); });

    var forceTab = @json($tabError);
    var saved = null;
    try { saved = localStorage.getItem('konfig-tab'); } catch (e) {}
    var awal = forceTab || saved;
    if (awal && tabs.some(function (t) { return t.dataset.tab === awal; })) show(awal);

    /* ---- Kartu status hidup ---- */
    var maint = form.elements['mode_pemeliharaan'];
    var aman = ['aman_captcha', 'aman_ip', 'aman_backup'].map(function (n) { return form.elements[n]; });
    function sync() {
        document.getElementById('sys-value').textContent = maint.checked ? 'Pemeliharaan' : 'Aktif / Online';
        document.getElementById('card-sys').classList.toggle('is-maint', maint.checked);
        document.getElementById('mnt-value').textContent = maint.checked ? 'Aktif' : 'Nonaktif';
        document.getElementById('sec-value').textContent =
            aman.filter(function (c) { return c.checked; }).length + ' / 3 Proteksi Aktif';
    }
    function ubah() { sync(); bar.classList.add('show'); }
    form.addEventListener('input', ubah);
    form.addEventListener('change', ubah);
    form.addEventListener('reset', function () {
        setTimeout(function () { sync(); bar.classList.remove('show'); }, 0);
    });

    /* ---- Notifikasi kecil ---- */
    function toast(msg) {
        toastEl.textContent = msg;
        toastEl.classList.add('show');
        clearTimeout(timer);
        timer = setTimeout(function () { toastEl.classList.remove('show'); }, 2800);
    }
    [].forEach.call(document.querySelectorAll('[data-soon]'), function (b) {
        b.addEventListener('click', function () { toast(b.dataset.soon); });
    });

    /* ---- Pratinjau logo (belum disimpan ke server) ---- */
    var file = document.getElementById('logo-input');
    file.addEventListener('change', function () {
        if (!file.files[0]) return;
        var r = new FileReader();
        r.onload = function (e) {
            var box = document.getElementById('logo-preview');
            box.innerHTML = '';
            var img = document.createElement('img');
            img.src = e.target.result;
            box.appendChild(img);
        };
        r.readAsDataURL(file.files[0]);
    });
})();
</script>
@endsection
