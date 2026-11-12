@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="greeting">Halo, Super Admin</h1>
    <p class="sub">Selamat datang di panel kontrol utama platform SawitSupply.</p>

    {{-- KARTU STATISTIK --}}
    <section class="stats">
        @foreach ($stats as $s)
            <div class="card stat">
                <div>
                    <span class="stat-label">{{ $s['label'] }}</span>
                    <strong class="stat-value">{{ $s['value'] }}</strong>
                    <small>{{ $s['note'] }}</small>
                </div>
                <span class="icon-btn square"></span>
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
                        <td><span class="badge pending">Pending</span></td>
                        <td class="center">
                            <button class="btn dark">Setujui</button>
                            <button class="btn light">Tolak</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    {{-- GRAFIK + TRANSAKSI --}}
    <section class="grid-2">
        <div class="card">
            <h2 class="card-title">Statistik Transaksi Platform Bulanan</h2>
            <div class="chart">
                @foreach ($monthly as $bulan => $persen)
                    <div class="bar-col">
                        <div class="bar {{ $loop->last ? 'highlight' : '' }}" style="height: {{ $persen }}%"></div>
                        <span>{{ $bulan }}</span>
                    </div>
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
                            <td><span class="badge {{ strtolower($t['status']) }}">{{ $t['status'] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
