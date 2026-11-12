<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - SawitSupply</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="app">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-mark">SS</span>
            <span class="brand-name">SawitSupply</span>
            <small>Super Admin Panel</small>
        </div>

        @php
            $menu = [
                ['admin.dashboard', 'Dashboard'],
                ['admin.toko', 'Kelola Toko (Verifikasi)'],
                ['admin.produk', 'Kelola Produk'],
                ['admin.pengguna', 'Kelola Pengguna'],
                ['admin.transaksi', 'Kelola Transaksi'],
                ['admin.konfigurasi', 'Konfigurasi Sistem'],
                ['admin.laporan', 'Laporan Global'],
            ];
        @endphp

        <nav class="menu">
            @foreach ($menu as [$route, $label])
                <a href="{{ route($route) }}" class="{{ request()->routeIs($route) ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <form method="POST" action="#" class="logout">
            @csrf
            <button type="button">[-&gt; Log Out</button>
        </form>
    </aside>

    {{-- KONTEN --}}
    <div class="main">
        <header class="topbar">
            <span>@yield('breadcrumb', 'Dashboard Super Admin / Ringkasan')</span>
            <div class="topbar-right">
                <span class="icon-btn"></span>
                <span class="profile"><i>A</i> Admin</span>
            </div>
        </header>

        <main class="content">
            @yield('content')
        </main>

        <footer class="footer">SawitSupply - Bengkel Pemrograman Web - Politeknik Caltex Riau</footer>
    </div>
</div>
</body>
</html>
