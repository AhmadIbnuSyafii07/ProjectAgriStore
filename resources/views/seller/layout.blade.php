<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | SawitSupply Penjual</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/seller.css') }}">
</head>
<body>
@php
    $me       = auth()->user();
    $shop     = $me->name;
    $initial  = strtoupper(mb_substr($shop, 0, 1));
    $newCount = \App\Models\Order::where('vendor_id', $me->id)->whereIn('order_status', ['pending_payment', 'processing'])->count();
    $logout   = \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('logout');
@endphp

<div class="shell" id="shell">
    <aside class="sidebar">
        <a href="{{ route('seller.dashboard') }}" class="brand">SS</a>

        <nav class="nav">
            <a href="{{ route('seller.dashboard') }}" class="nav-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
                <x-seller.icon name="home" /> <span>Home</span>
            </a>

            <details class="nav-group" {{ request()->routeIs('seller.products.*') ? 'open' : '' }}>
                <summary class="nav-link {{ request()->routeIs('seller.products.*') ? 'active' : '' }}">
                    <x-seller.icon name="box" /> <span>My Products</span>
                    <x-seller.icon name="chevron" :size="14" class="icon caret" />
                </summary>
                <a href="{{ route('seller.products.create') }}" class="nav-sub">Add</a>
                <a href="{{ route('seller.products.index') }}" class="nav-sub">Edit</a>
            </details>

            <a href="{{ route('seller.orders.index', ['status' => 'new']) }}" class="nav-link {{ request()->routeIs('seller.orders.*') ? 'active' : '' }}">
                <x-seller.icon name="cart" /> <span>New Orders</span>
                @if ($newCount > 0)<span class="badge">{{ $newCount }}</span>@endif
            </a>
            <a href="{{ route('seller.stock.index') }}" class="nav-link {{ request()->routeIs('seller.stock.*') ? 'active' : '' }}">
                <x-seller.icon name="stock" /> <span>Manage Stock</span>
            </a>
            <a href="{{ route('seller.reports.index') }}" class="nav-link {{ request()->routeIs('seller.reports.*') ? 'active' : '' }}">
                <x-seller.icon name="report" /> <span>Sales Reports</span>
            </a>
            <a href="{{ route('seller.profile.edit') }}" class="nav-link {{ request()->routeIs('seller.profile.*') ? 'active' : '' }}">
                <x-seller.icon name="user" /> <span>Shop Profile</span>
            </a>
        </nav>

        <div class="side-actions">
            <a href="{{ route('seller.products.create') }}" class="btn btn-primary"><x-seller.icon name="plus" :size="16" /> Add Product</a>
            <a href="{{ route('seller.orders.index') }}" class="btn btn-outline"><x-seller.icon name="list" :size="16" /> View Orders</a>
        </div>

        <form method="POST" action="{{ $logout }}" class="logout">
            @csrf
            <button type="submit" class="nav-link"><x-seller.icon name="logout" /> <span>Log Out</span></button>
        </form>
    </aside>

    <div class="main">
        <header class="topbar">
            <button class="menu-btn" id="menuBtn" type="button" aria-label="Buka menu"><x-seller.icon name="menu" :size="22" /></button>
            <div class="topbar-right">
                <a href="{{ route('seller.orders.index', ['status' => 'new']) }}" class="icon-btn" aria-label="Pesanan baru">
                    <x-seller.icon name="bell" />@if ($newCount > 0)<span class="dot"></span>@endif
                </a>
                <a href="{{ route('seller.orders.index') }}" class="icon-btn" aria-label="Pesanan"><x-seller.icon name="cart" /></a>
                <a href="{{ route('seller.profile.edit') }}" class="profile">
                    <span class="avatar">{{ $initial }}</span>
                    <span class="profile-name">{{ $shop }}</span>
                </a>
            </div>
        </header>

        <main class="content">
            @if (session('success'))<div class="alert alert-ok">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="alert alert-err">{{ session('error') }}</div>@endif
            @if ($errors->any())
                <div class="alert alert-err">
                    <strong>Periksa kembali isian Anda:</strong>
                    <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
<div class="scrim" id="scrim"></div>
<script>
    var shell = document.getElementById('shell');
    document.getElementById('menuBtn').addEventListener('click', function () { shell.classList.toggle('open'); });
    document.getElementById('scrim').addEventListener('click', function () { shell.classList.remove('open'); });
</script>
</body>
</html>
