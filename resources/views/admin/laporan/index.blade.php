@extends('layouts.layouts_admin.admin')

@section('title', 'Laporan Global')
@section('breadcrumb', 'Dashboard Super Admin / Laporan Global')

@section('content')
@php
    $persenVerif = $ringkasan['total_toko'] > 0
        ? round($ringkasan['vendor_aktif'] / $ringkasan['total_toko'] * 100)
        : 0;
    $naik = $ringkasan['pertumbuhan'] >= 0;
@endphp

{{-- ============ 4 KARTU RINGKASAN ============ --}}
<section class="p-stats">
    <div class="p-stat kc lp-c1">
        <div class="p-stat-top"><span>Total Pendapatan Platform</span><span class="p-icon"><x-admin.icon name="trend" :size="15" /></span></div>
        <strong>Rp {{ number_format($ringkasan['pendapatan'], 0, ',', '.') }}</strong>
        <span class="p-sub">{{ $naik ? '+' : '' }}{{ $ringkasan['pertumbuhan'] }}% dari bulan lalu</span>
    </div>
    <div class="p-stat kc lp-c2">
        <div class="p-stat-top"><span>Total Transaksi Sukses</span><span class="p-icon"><x-admin.icon name="check" :size="15" /></span></div>
        <strong>{{ number_format($ringkasan['transaksi'], 0, ',', '.') }}</strong>
        <span class="p-sub">Pesanan selesai</span>
    </div>
    <div class="p-stat kc lp-c3">
        <div class="p-stat-top"><span>Produk Terlaris</span><span class="p-icon"><x-admin.icon name="star" :size="15" /></span></div>
        <strong>{{ $ringkasan['terlaris'] ?? '-' }}</strong>
        <span class="p-sub">{{ number_format($ringkasan['terjual'], 0, ',', '.') }} terjual</span>
    </div>
    <div class="p-stat kc lp-c4">
        <div class="p-stat-top"><span>Vendor Aktif</span><span class="p-icon"><x-admin.icon name="store" :size="15" /></span></div>
        <strong>{{ $ringkasan['vendor_aktif'] }} Toko</strong>
        <span class="p-sub">{{ $persenVerif }}% dari {{ $ringkasan['total_toko'] }} toko terverifikasi</span>
    </div>
</section>

{{-- ============ FILTER ============ --}}
<form method="GET" action="{{ route('admin.laporan') }}" class="lp-filter" id="lp-form">
    <div class="lp-date">
        <x-admin.icon name="calendar" :size="15" />
        <input type="date" name="dari" value="{{ $dari->toDateString() }}" aria-label="Dari tanggal">
        <span>–</span>
        <input type="date" name="sampai" value="{{ $sampai->toDateString() }}" aria-label="Sampai tanggal">
    </div>

    <div class="lp-presets">
        <button type="button" data-preset="30">30 Hari</button>
        <button type="button" data-preset="bulan">Bulan Ini</button>
        <button type="button" data-preset="tahun">Tahun Ini</button>
    </div>

    <select name="kategori" class="lp-select" onchange="this.form.submit()">
        <option value="">Semua Kategori</option>
        @foreach ($kategori as $k)
            <option value="{{ $k }}" @selected(request('kategori') === $k)>{{ $k }}</option>
        @endforeach
    </select>

    <button type="submit" class="lp-btn soft">Terapkan</button>

    <div class="lp-export">
        <button type="button" class="lp-btn pdf" data-soon="Export PDF aktif setelah data transaksi terhubung."><x-admin.icon name="file" :size="14" /> Export PDF</button>
        <button type="button" class="lp-btn xls" data-soon="Export Excel aktif setelah data transaksi terhubung."><x-admin.icon name="grid" :size="14" /> Export Excel</button>
    </div>
</form>

<div class="lp-period">
    <span>Periode laporan: <strong>{{ $dari->locale('id')->translatedFormat('d M Y') }} – {{ $sampai->locale('id')->translatedFormat('d M Y') }}</strong></span>
    <label class="lp-demo">
        <span>Pratinjau data contoh</span>
        <span class="switch"><input type="checkbox" id="lp-demo"><span class="slider"></span></span>
    </label>
</div>

{{-- ============ GRAFIK ============ --}}
<section class="lp-grid">
    <div class="card lp-card">
        <div class="lp-card-head">
            <h2 class="lp-title">Pertumbuhan Pendapatan &amp; Transaksi ({{ $sampai->year }})</h2>
            <div class="lp-legends">
                <button type="button" class="lp-leg" data-series="pendapatan"><i style="background:#3b6df0"></i> Pendapatan</button>
                <button type="button" class="lp-leg" data-series="transaksi"><i style="background:#22b573"></i> Transaksi</button>
            </div>
        </div>
        <div class="lp-chart" id="lp-line">
            <div class="lp-empty" id="lp-line-empty">
                <span class="ic"><x-admin.icon name="chart" :size="20" /></span>
                <strong>Belum ada data transaksi</strong>
                <span>Grafik akan terisi otomatis saat pesanan masuk.</span>
            </div>
            <div class="lp-tip" id="lp-tip"></div>
        </div>
    </div>

    <div class="card lp-card">
        <div class="lp-card-head">
            <h2 class="lp-title">Distribusi Penjualan per Kategori</h2>
        </div>
        <div class="lp-donut" id="lp-donut"></div>
        <ul class="lp-dl" id="lp-dl"></ul>
    </div>
</section>

{{-- ============ REKAP VENDOR ============ --}}
<section class="card lp-card">
    <div class="lp-card-head">
        <h2 class="lp-title">Rekapitulasi Performa Vendor</h2>
        <span class="lp-note">Angka performa terisi setelah data penjualan terhubung</span>
    </div>

    <table class="table lp-table">
        <thead>
            <tr>
                <th>Nama Vendor</th>
                <th>Total Produk</th>
                <th>Pesanan Selesai</th>
                <th>Total Omzet</th>
                <th>Kontribusi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($vendor as $v)
                @php $kontribusi = $totalOmzet > 0 ? round($v->omzet / $totalOmzet * 100) : 0; @endphp
                <tr>
                    <td>
                        <div class="lp-vendor">
                            <span class="lp-av">{{ strtoupper(mb_substr($v->nama, 0, 1)) }}</span>
                            <div>
                                <strong>{{ $v->nama }}</strong>
                                <span class="sub">{{ $v->wilayah }}</span>
                            </div>
                        </div>
                    </td>
                    <td>{{ number_format($v->produk, 0, ',', '.') }}</td>
                    <td>{{ number_format($v->pesanan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($v->omzet, 0, ',', '.') }}</td>
                    <td>
                        <div class="lp-bar"><span><i style="width: {{ $kontribusi }}%"></i></span><b>{{ $kontribusi }}%</b></div>
                    </td>
                    <td><span class="pill s-{{ $v->status }}">{{ ucfirst($v->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Belum ada vendor aktif. Vendor muncul di sini setelah toko disetujui di Kelola Toko.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="lp-foot">
        <span>Menampilkan {{ $vendor->count() }} dari {{ $vendor->total() }} vendor aktif</span>
        {{ $vendor->links('pagination::bootstrap-4') }}
    </div>
</section>

<div class="k-toast" id="lp-toast"></div>

<script>
(function () {
    const bulan = @json($grafik['bulan']);
    const real = { pendapatan: @json($grafik['pendapatan']), transaksi: @json($grafik['transaksi']) };
    const realDist = @json($distribusi);
    const demo = {
        pendapatan: [8, 14, 11, 22, 31, 24, 35, 41, 38, 52, 47, 63].map(v => v * 1000000),
        transaksi:  [12, 20, 17, 29, 38, 31, 44, 52, 47, 60, 55, 71],
        dist:       [48e6, 27e6, 15e6, 10e6]
    };
    const warna = ['#3b6df0', '#f0605a', '#8b5cf6', '#f5a524', '#22b573', '#06b6d4'];
    const state = { demo: false, show: { pendapatan: true, transaksi: true } };
    const NS = 'http://www.w3.org/2000/svg';

    /* ---------- helper ---------- */
    const rp = v => v >= 1e9 ? (v / 1e9).toFixed(1).replace('.0', '') + ' M'
                 : v >= 1e6 ? Math.round(v / 1e6) + ' jt'
                 : v >= 1e3 ? Math.round(v / 1e3) + ' rb' : String(Math.round(v));
    const rpFull = v => 'Rp ' + Math.round(v).toLocaleString('id-ID');
    const nice = (max, fb) => {
        if (!(max > 0)) return fb;
        const e = Math.pow(10, Math.floor(Math.log10(max))), f = max / e;
        return (f <= 1 ? 1 : f <= 2 ? 2 : f <= 5 ? 5 : 10) * e;
    };
    function smooth(p) {
        let d = 'M' + p[0][0].toFixed(1) + ',' + p[0][1].toFixed(1);
        for (let i = 0; i < p.length - 1; i++) {
            const a = p[Math.max(i - 1, 0)], b = p[i], c = p[i + 1], e = p[Math.min(i + 2, p.length - 1)];
            d += ' C' + (b[0] + (c[0] - a[0]) / 6).toFixed(1) + ',' + (b[1] + (c[1] - a[1]) / 6).toFixed(1) +
                 ' ' + (c[0] - (e[0] - b[0]) / 6).toFixed(1) + ',' + (c[1] - (e[1] - b[1]) / 6).toFixed(1) +
                 ' ' + c[0].toFixed(1) + ',' + c[1].toFixed(1);
        }
        return d;
    }

    /* ---------- grafik garis/area ---------- */
    const wrap = document.getElementById('lp-line');
    const tip = document.getElementById('lp-tip');
    const emptyBox = document.getElementById('lp-line-empty');

    function renderLine() {
        const old = wrap.querySelector('svg');
        if (old) old.remove();

        const src = state.demo ? demo : real;
        const kosong = !state.demo && src.pendapatan.every(v => v === 0) && src.transaksi.every(v => v === 0);
        emptyBox.classList.toggle('show', kosong);

        const W = wrap.clientWidth || 600, H = wrap.clientHeight || 270;
        const m = { l: 46, r: 40, t: 14, b: 28 };
        const iw = W - m.l - m.r, ih = H - m.t - m.b, n = bulan.length;
        const maxP = nice(Math.max.apply(null, src.pendapatan), 100e6);
        const maxT = nice(Math.max.apply(null, src.transaksi), 100);
        const X = i => m.l + i * iw / (n - 1);
        const ptsP = src.pendapatan.map((v, i) => [X(i), m.t + ih - v / maxP * ih]);
        const ptsT = src.transaksi.map((v, i) => [X(i), m.t + ih - v / maxT * ih]);

        let s = '<defs>' +
            '<linearGradient id="gP" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3b6df0" stop-opacity=".35"/><stop offset="1" stop-color="#3b6df0" stop-opacity="0"/></linearGradient>' +
            '<linearGradient id="gT" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#22b573" stop-opacity=".3"/><stop offset="1" stop-color="#22b573" stop-opacity="0"/></linearGradient></defs>';

        for (let g = 0; g <= 4; g++) {
            const y = m.t + ih - g * ih / 4;
            s += '<line x1="' + m.l + '" x2="' + (W - m.r) + '" y1="' + y + '" y2="' + y + '" stroke="#e8ebf3" stroke-dasharray="' + (g ? '3 4' : '0') + '"/>' +
                 '<text x="' + (m.l - 8) + '" y="' + (y + 3.5) + '" text-anchor="end">' + rp(maxP * g / 4) + '</text>' +
                 '<text x="' + (W - m.r + 8) + '" y="' + (y + 3.5) + '">' + Math.round(maxT * g / 4) + '</text>';
        }
        bulan.forEach((b, i) => { s += '<text x="' + X(i) + '" y="' + (H - 8) + '" text-anchor="middle">' + b + '</text>'; });

        [['transaksi', ptsT, '#22b573', 'gT'], ['pendapatan', ptsP, '#3b6df0', 'gP']].forEach(function (c) {
            if (!state.show[c[0]]) return;
            const d = smooth(c[1]);
            s += '<path class="lp-area" d="' + d + ' L' + c[1][n - 1][0] + ',' + (m.t + ih) + ' L' + c[1][0][0] + ',' + (m.t + ih) + ' Z" fill="url(#' + c[3] + ')"/>' +
                 '<path class="lp-linepath" pathLength="1" d="' + d + '" fill="none" stroke="' + c[2] + '" stroke-width="2.6" stroke-linecap="round"/>';
        });

        s += '<line id="hv" y1="' + m.t + '" y2="' + (m.t + ih) + '" stroke="#9aa3b8" stroke-dasharray="3 3" opacity="0"/>' +
             '<circle id="hp" r="4.5" fill="#fff" stroke="#3b6df0" stroke-width="2.5" opacity="0"/>' +
             '<circle id="ht" r="4.5" fill="#fff" stroke="#22b573" stroke-width="2.5" opacity="0"/>' +
             '<rect id="hr" x="' + m.l + '" y="' + m.t + '" width="' + iw + '" height="' + ih + '" fill="transparent"/>';

        const svg = document.createElementNS(NS, 'svg');
        svg.setAttribute('width', W); svg.setAttribute('height', H);
        svg.innerHTML = s;
        wrap.insertBefore(svg, wrap.firstChild);

        const hv = svg.querySelector('#hv'), hp = svg.querySelector('#hp'), ht = svg.querySelector('#ht'), hr = svg.querySelector('#hr');
        function off() { [hv, hp, ht].forEach(e => e.setAttribute('opacity', 0)); tip.style.opacity = 0; }
        hr.addEventListener('mousemove', function (ev) {
            const box = svg.getBoundingClientRect();
            const i = Math.max(0, Math.min(n - 1, Math.round((ev.clientX - box.left - m.l) / (iw / (n - 1)))));
            hv.setAttribute('x1', X(i)); hv.setAttribute('x2', X(i)); hv.setAttribute('opacity', 1);
            hp.setAttribute('cx', X(i)); hp.setAttribute('cy', ptsP[i][1]); hp.setAttribute('opacity', state.show.pendapatan ? 1 : 0);
            ht.setAttribute('cx', X(i)); ht.setAttribute('cy', ptsT[i][1]); ht.setAttribute('opacity', state.show.transaksi ? 1 : 0);
            tip.innerHTML = '<b>' + bulan[i] + '</b>' +
                (state.show.pendapatan ? '<br><span style="color:#9db8ff">●</span> ' + rpFull(src.pendapatan[i]) : '') +
                (state.show.transaksi ? '<br><span style="color:#7be3b0">●</span> ' + src.transaksi[i] + ' transaksi' : '');
            tip.style.left = X(i) + 'px';
            tip.style.top = Math.min(ptsP[i][1], ptsT[i][1]) + 'px';
            tip.style.opacity = 1;
        });
        hr.addEventListener('mouseleave', off);
    }

    /* ---------- donut ---------- */
    const donut = document.getElementById('lp-donut');
    const dl = document.getElementById('lp-dl');

    function renderDonut() {
        const items = realDist.map((d, i) => ({ k: d.kategori, v: state.demo ? (demo.dist[i] || 0) : d.nilai, c: warna[i % warna.length] }));
        const total = items.reduce((a, b) => a + b.v, 0);
        const R = 44, C = 2 * Math.PI * R;
        let off = 0, segs = '';
        items.forEach(function (it, i) {
            const len = total ? it.v / total * C : 0;
            if (len > 0) {
                const dash = Math.max(len - 2.5, 0.5);
                segs += '<circle class="seg" data-i="' + i + '" cx="60" cy="60" r="' + R + '" fill="none" stroke="' + it.c + '" stroke-width="15" ' +
                        'stroke-dasharray="0 ' + C + '" data-dash="' + dash + ' ' + (C - dash) + '" stroke-dashoffset="' + (-off) + '" transform="rotate(-90 60 60)" stroke-linecap="round"/>';
            }
            off += len;
        });
        donut.innerHTML =
            '<svg viewBox="0 0 120 120"><circle cx="60" cy="60" r="' + R + '" fill="none" stroke="#eef1f6" stroke-width="15"/>' + segs + '</svg>' +
            '<div class="lp-donut-c"><b id="dc-a">' + (total ? rp(total) : '0') + '</b><span id="dc-b">' + (total ? 'Total penjualan' : 'Belum ada data') + '</span></div>';

        requestAnimationFrame(function () {
            donut.querySelectorAll('.seg').forEach(function (c) { c.setAttribute('stroke-dasharray', c.dataset.dash); });
        });

        const a = donut.querySelector('#dc-a'), b = donut.querySelector('#dc-b');
        const base = [a.textContent, b.textContent];
        donut.querySelectorAll('.seg').forEach(function (c) {
            c.addEventListener('mouseenter', function () {
                const it = items[c.dataset.i];
                a.textContent = Math.round(it.v / total * 100) + '%'; b.textContent = it.k;
            });
            c.addEventListener('mouseleave', function () { a.textContent = base[0]; b.textContent = base[1]; });
        });

        dl.innerHTML = items.map(function (it) {
            const p = total ? Math.round(it.v / total * 100) : 0;
            return '<li><span><i style="background:' + it.c + '"></i>' + it.k + '</span><b>' + p + '%</b></li>';
        }).join('');
    }

    function renderAll() { renderLine(); renderDonut(); }

    /* ---------- kontrol ---------- */
    document.getElementById('lp-demo').addEventListener('change', function (e) { state.demo = e.target.checked; renderAll(); });
    document.querySelectorAll('[data-series]').forEach(function (b) {
        b.addEventListener('click', function () {
            const k = b.dataset.series;
            state.show[k] = !state.show[k];
            b.classList.toggle('off', !state.show[k]);
            renderLine();
        });
    });

    const form = document.getElementById('lp-form');
    const iso = d => d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    document.querySelectorAll('[data-preset]').forEach(function (b) {
        b.addEventListener('click', function () {
            const t = new Date(); let f = new Date();
            if (b.dataset.preset === '30') f.setDate(t.getDate() - 29);
            if (b.dataset.preset === 'bulan') f = new Date(t.getFullYear(), t.getMonth(), 1);
            if (b.dataset.preset === 'tahun') f = new Date(t.getFullYear(), 0, 1);
            form.elements['dari'].value = iso(f);
            form.elements['sampai'].value = iso(t);
            form.submit();
        });
    });

    const toastEl = document.getElementById('lp-toast'); let timer;
    document.querySelectorAll('[data-soon]').forEach(function (b) {
        b.addEventListener('click', function () {
            toastEl.textContent = b.dataset.soon; toastEl.classList.add('show');
            clearTimeout(timer); timer = setTimeout(function () { toastEl.classList.remove('show'); }, 2800);
        });
    });

    let rz; window.addEventListener('resize', function () { clearTimeout(rz); rz = setTimeout(renderLine, 150); });
    renderAll();
})();
</script>
@endsection
