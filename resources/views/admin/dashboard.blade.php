@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    {{-- KARTU STATISTIK --}}
    <section class="stats">
        @foreach ($stats as $s)
            <div class="stat {{ $s['color'] }}">
                <div class="stat-head">
                    <span class="stat-title">{{ $s['title'] }}</span>
                    <span class="stat-icon"><x-icon :name="$s['icon']" :size="18" /></span>
                </div>
                <strong class="stat-value">{{ $s['value'] }}</strong>
                <div class="stat-foot">
                    <div>
                        <span>{{ $s['label'] }}</span>
                        <span>{{ $s['note'] }}</span>
                    </div>
                    <svg viewBox="0 0 100 40" preserveAspectRatio="none" class="spark">
                        <path d="{{ $s['path']['area'] }}" fill="rgba(255,255,255,.22)"/>
                        <path d="{{ $s['path']['line'] }}" fill="none" stroke="#fff" stroke-width="2" vector-effect="non-scaling-stroke"/>
                    </svg>
                </div>
            </div>
        @endforeach
    </section>

    {{-- VERIFIKASI TOKO --}}
    <section class="card">
        <h2 class="card-title">Toko Baru Menunggu Persetujuan (Verifikasi Vendor)</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Toko</th><th>Email Pendaftar</th><th>Tanggal Daftar</th>
                    <th>Status</th><th class="center">Aksi Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pendingStores as $toko)
                    <tr>
                        <td>{{ $toko['nama'] }}</td>
                        <td>{{ $toko['email'] }}</td>
                        <td>{{ $toko['tanggal'] }}</td>
                        <td><span class="pill pending">Pending</span></td>
                        <td class="center">
                            <button class="btn approve"><x-icon name="check" :size="12" /> Setujui</button>
                            <button class="btn reject"><x-icon name="x" :size="12" /> Tolak</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    {{-- GRAFIK + TRANSAKSI --}}
    <section class="grid-2">
        <div class="card">
            <h2 class="card-title">Statistik Transaksi Bulanan</h2>
            <svg viewBox="0 0 400 200" preserveAspectRatio="none" class="area-chart">
                <defs>
                    <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0" stop-color="#22b8f0"/>
                        <stop offset=".55" stop-color="#3b82f6"/>
                        <stop offset="1" stop-color="#a855f7"/>
                    </linearGradient>
                </defs>
                <path d="{{ $chart['area'] }}" fill="url(#areaGrad)"/>
                <path d="{{ $chart['line'] }}" fill="none" stroke="#0ea5e9" stroke-width="2.5" vector-effect="non-scaling-stroke"/>
            </svg>
            <div class="months">
                @foreach ($monthly as $bulan => $nilai)
                    <span>{{ $bulan }}</span>
                @endforeach
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Transaksi Terbaru Global</h2>
            <table class="table compact">
                <thead>
                    <tr><th>No. Invoice</th><th>Pembeli</th><th>Vendor</th><th>Total</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $t)
                        <tr>
                            <td>{{ $t['invoice'] }}</td>
                            <td>{{ $t['pembeli'] }}</td>
                            <td>{{ $t['vendor'] }}</td>
                            <td>{{ $t['total'] }}</td>
                            <td><span class="tag {{ strtolower($t['status']) }}">{{ $t['status'] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
