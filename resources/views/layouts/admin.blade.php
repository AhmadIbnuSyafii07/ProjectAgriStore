<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - SawitSupply</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="app">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-mark">SS</span>
            <span class="brand-name">SawitSupply</span>
        </div>

        <div class="user-card">
            <span class="avatar">A</span>
            <div>
                <strong>Ahmad Ibnu Syafii</strong>
                <small>admin@sawitsupply.com</small>
            </div>
        </div>

        @php
            $menu = [
                ['admin.dashboard', 'Dashboard', 'grid'],
                ['admin.toko', 'Kelola Toko', 'store'],
                ['admin.produk', 'Kelola Produk', 'box'],
                ['admin.pengguna', 'Kelola Pengguna', 'users'],
                ['admin.transaksi', 'Kelola Transaksi', 'repeat'],
                ['admin.konfigurasi', 'Konfigurasi Sistem', 'sliders'],
                ['admin.laporan', 'Laporan Global', 'file'],
            ];
        @endphp

        <nav class="menu">
            @foreach ($menu as [$route, $label, $icon])
                <a href="{{ route($route) }}" class="{{ request()->routeIs($route) ? 'active' : '' }}">
                    <x-icon :name="$icon" :size="17" /> {{ $label }}
                    @if ($route === 'admin.toko' && ($menunggu = \App\Models\Toko::where('status', 'menunggu')->count()) > 0)
                        <span class="menu-badge">{{ $menunggu }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <form method="POST" action="#" class="logout">
            @csrf
            <button type="button"><x-icon name="logout" :size="17" /> Log Out</button>
        </form>
    </aside>

    {{-- KONTEN --}}
    <div class="main">
        <header class="topbar">
            <strong class="crumb">@yield('breadcrumb', 'Dashboard Super Admin / Ringkasan')</strong>
            <div class="topbar-right">
                <div class="search">
                    <input type="text" placeholder="Search...">
                    <button type="button" aria-label="Cari"><x-icon name="search" :size="16" /></button>
                </div>
                <span class="bell"><x-icon name="bell" :size="19" /><i>1</i></span>
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
