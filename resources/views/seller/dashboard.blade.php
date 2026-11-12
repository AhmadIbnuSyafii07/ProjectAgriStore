@php
    // ====== DATA DUMMY (nanti ganti dengan data dari database) ======
    $shop = (auth()->check() && auth()->user()->name) ? auth()->user()->name : 'Toko Sawit Sejahtera';
    $initial = strtoupper(substr($shop, 0, 1));
    $logoutUrl = \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('logout');

    $stats = ['new_orders' => 12, 'active_products' => 55, 'low_stock' => 3, 'month_revenue' => 15000000];
    $weekly = ['Sen' => 8, 'Sel' => 14, 'Rab' => 10, 'Kam' => 18, 'Jum' => 12, 'Sab' => 15, 'Min' => 7];
    $monthly = ['Jan' => 9, 'Feb' => 11, 'Mar' => 14, 'Apr' => 12, 'Mei' => 15, 'Jun' => 13,
                'Jul' => 16, 'Agu' => 14, 'Sep' => 18, 'Okt' => 17, 'Nov' => 15, 'Des' => 20];
    $statusBreakdown = ['processed' => 45, 'shipped' => 30, 'pending' => 25];
    $statusLabel = ['processed' => 'Diproses', 'shipped' => 'Dikirim', 'pending' => 'Menunggu'];
    $orders = [
        ['code' => '#SS-001', 'buyer' => 'Budi Santoso', 'product' => 'Pupuk NPK 13-6-27', 'total' => 'Rp 300.000', 'status' => 'processed'],
        ['code' => '#SS-002', 'buyer' => 'Siti Aminah',  'product' => 'Pupuk NPK 13-6-27', 'total' => 'Rp 250.000', 'status' => 'processed'],
        ['code' => '#SS-003', 'buyer' => 'Andi Pratama', 'product' => 'Egrek Sawit',       'total' => 'Rp 450.000', 'status' => 'shipped'],
        ['code' => '#SS-004', 'buyer' => 'Rina Marlina', 'product' => 'Pupuk NPK 13-6-37', 'total' => 'Rp 500.000', 'status' => 'pending'],
        ['code' => '#SS-005', 'buyer' => 'Joko Widodo',  'product' => 'Dodos Sawit',       'total' => 'Rp 180.000', 'status' => 'pending'],
    ];
    $lowStockItems = [
        ['name' => 'Pupuk Urea 50kg', 'stock' => 4, 'max' => 40],
        ['name' => 'Egrek Sawit',     'stock' => 6, 'max' => 30],
        ['name' => 'Sarung Tangan',   'stock' => 9, 'max' => 60],
    ];
    $newOrderCount = 5;

    // ====== PERHITUNGAN ======
    $weeklyMax = max($weekly);
    $monthlyMax = max($monthly);
    $p = $statusBreakdown['processed'];
    $s = $p + $statusBreakdown['shipped'];
    $donut = 'conic-gradient(#1B2A4A 0 ' . $p . '%, #2F8F83 ' . $p . '% ' . $s . '%, #E8590C ' . $s . '% 100%)';

    // ====== IKON SVG ======
    $paths = [
        'home'    => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>',
        'box'     => '<path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/>',
        'cart'    => '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.7 11.4a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.8L20 7H6"/>',
        'stock'   => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'report'  => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h6"/>',
        'user'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'plus'    => '<path d="M12 5v14M5 12h14"/>',
        'list'    => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/>',
        'logout'  => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>',
        'bell'    => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
        'check'   => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M8.5 12l2.5 2.5L16 9.5"/>',
        'alert'   => '<path d="M12 3l10 18H2L12 3z"/><path d="M12 10v5M12 18h.01"/>',
        'wallet'  => '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18"/><path d="M16 15h2"/>',
        'chevron' => '<path d="M6 9l6 6 6-6"/>',
        'menu'    => '<path d="M4 7h16M4 12h16M4 17h16"/>',
    ];
    $icon = function ($name, $size = 18) use ($paths) {
        return '<svg class="icon" xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[$name] . '</svg>';
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Penjual | SawitSupply</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@verbatim
<style>
:root{--navy:#1B2A4A;--navy-2:#2A3F6B;--navy-soft:#E6EBF5;--orange:#E8590C;--orange-soft:#FDEBDD;--teal:#2F8F83;--teal-soft:#DDF1EE;--bg:#F3F5F9;--surface:#fff;--line:#DCE1EB;--ink:#18223A;--muted:#66718A;--r:12px;--sidebar:244px;--font:'Plus Jakarta Sans',system-ui,-apple-system,'Segoe UI',Roboto,sans-serif}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font);background:var(--bg);color:var(--ink);font-size:14px;line-height:1.5}
a{color:inherit;text-decoration:none}
button{font:inherit;color:inherit;background:none;border:0;cursor:pointer}
.icon{flex:none}
.shell{display:grid;grid-template-columns:var(--sidebar) 1fr;min-height:100vh}
.sidebar{background:var(--surface);border-right:1px solid var(--line);padding:20px 14px;display:flex;flex-direction:column;gap:18px;position:sticky;top:0;height:100vh;overflow-y:auto}
.brand{font-weight:800;font-size:30px;letter-spacing:-1px;color:var(--navy);padding:0 10px}
.nav{display:flex;flex-direction:column;gap:2px}
.nav-link{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;color:var(--muted);font-weight:600;width:100%;text-align:left;list-style:none;cursor:pointer}
.nav-link:hover{background:var(--bg);color:var(--ink)}
.nav-link.active{background:var(--navy);color:#fff}
.nav-group summary::-webkit-details-marker{display:none}
.nav-group .caret{margin-left:auto;transition:transform .15s}
.nav-group[open] .caret{transform:rotate(180deg)}
.nav-sub{display:block;padding:6px 10px 6px 38px;color:var(--muted);font-weight:500}
.nav-sub:hover{color:var(--navy)}
.badge{margin-left:auto;background:var(--orange);color:#fff;font-size:11px;font-weight:700;min-width:20px;height:20px;border-radius:10px;display:grid;place-items:center;padding:0 6px}
.side-actions{display:flex;flex-direction:column;gap:8px;padding:0 4px}
.logout{margin-top:auto}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:9px 14px;border-radius:8px;font-weight:700;border:1.5px solid transparent}
.btn-primary{background:var(--navy);color:#fff}
.btn-primary:hover{background:var(--navy-2)}
.btn-outline{border-color:var(--navy);color:var(--navy)}
.btn-outline:hover{background:var(--navy);color:#fff}
.btn-sm{padding:3px 12px;font-size:12px}
.topbar{display:flex;align-items:center;padding:14px 28px;background:var(--surface);border-bottom:1px solid var(--line);position:sticky;top:0;z-index:5}
.topbar-right{display:flex;align-items:center;gap:6px;margin-left:auto}
.icon-btn{position:relative;width:38px;height:38px;border-radius:50%;display:grid;place-items:center;color:var(--muted)}
.icon-btn:hover{background:var(--bg)}
.icon-btn .dot{position:absolute;top:8px;right:9px;width:8px;height:8px;border-radius:50%;background:var(--orange);border:2px solid #fff}
.profile{display:flex;align-items:center;gap:10px;padding:4px 10px 4px 4px;border:1px solid var(--line);border-radius:999px;margin-left:8px}
.avatar{width:30px;height:30px;border-radius:50%;background:var(--navy);color:#fff;display:grid;place-items:center;font-weight:700;font-size:13px}
.profile-name{font-weight:600;font-size:13px}
.menu-btn{display:none}
.content{padding:28px;display:flex;flex-direction:column;gap:20px;max-width:1180px;width:100%}
.page-head h1{font-size:26px;font-weight:800;letter-spacing:-.4px;color:var(--navy)}
.page-head p{color:var(--muted);margin-top:2px}
.panel{background:var(--surface);border:1px solid var(--line);border-radius:var(--r);padding:18px 20px}
.panel h2{font-size:15px;font-weight:700;margin-bottom:14px}
.grid-2{display:grid;grid-template-columns:1.35fr 1fr;gap:20px}
.link{display:inline-block;margin-top:14px;font-weight:700;color:var(--orange)}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.stat{display:flex;align-items:center;gap:14px;background:var(--surface);border:1px solid var(--line);border-radius:var(--r);padding:16px}
.stat-ico{width:42px;height:42px;border-radius:10px;background:var(--navy-soft);color:var(--navy);display:grid;place-items:center;flex:none}
.stat small{display:block;color:var(--muted);font-size:12px;font-weight:600}
.stat strong{font-size:22px;font-weight:800;letter-spacing:-.4px}
.stat-warn .stat-ico{background:var(--orange-soft);color:var(--orange)}
.stat-warn strong{color:var(--orange)}
.bars{display:flex;align-items:flex-end;gap:10px;border-bottom:1px solid var(--line);margin-bottom:26px}
.bars-lg{height:170px}
.bars-sm{height:150px;gap:6px}
.bar-col{flex:1;height:100%;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;position:relative}
.bar{width:100%;background:var(--navy);border-radius:5px 5px 0 0;min-height:4px}
.bar-col:hover .bar{background:var(--orange)}
.bar-col span{position:absolute;bottom:-22px;font-size:11px;color:var(--muted)}
.donut-wrap{display:flex;align-items:center;gap:24px;flex-wrap:wrap}
.donut{width:150px;height:150px;border-radius:50%;display:grid;place-items:center;flex:none}
.donut-hole{width:92px;height:92px;border-radius:50%;background:var(--surface);display:flex;flex-direction:column;align-items:center;justify-content:center;line-height:1.1}
.donut-hole strong{font-size:22px;font-weight:800}
.donut-hole span{font-size:11px;color:var(--muted)}
.legend{list-style:none;display:flex;flex-direction:column;gap:8px;font-weight:600}
.sw{display:inline-block;width:10px;height:10px;border-radius:3px;margin-right:8px}
.sw-processed{background:var(--navy)}.sw-shipped{background:var(--teal)}.sw-pending{background:var(--orange)}
.table-panel{padding:0;overflow:hidden}
.table-scroll{overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:640px}
th{text-align:left;font-size:12px;color:var(--muted);font-weight:700;padding:12px 18px;background:#FAFBFD;border-bottom:1px solid var(--line)}
td{padding:12px 18px;border-bottom:1px solid var(--line)}
tbody tr:last-child td{border-bottom:0}
tbody tr:hover{background:#FAFBFD}
.mono{font-weight:700;color:var(--navy)}
.empty{text-align:center;color:var(--muted);padding:28px}
.chip{display:inline-block;padding:2px 12px;border-radius:999px;font-size:12px;font-weight:700}
.chip-processed{background:var(--navy-soft);color:var(--navy)}
.chip-shipped{background:var(--teal-soft);color:var(--teal)}
.chip-pending{background:var(--orange-soft);color:var(--orange)}
.stock-list{list-style:none;display:flex;flex-direction:column;gap:14px}
.stock-top{display:flex;justify-content:space-between;font-weight:600;margin-bottom:5px}
.stock-top b{color:var(--orange);font-size:12px}
.meter{height:8px;background:var(--bg);border-radius:99px;overflow:hidden}
.meter i{display:block;height:100%;background:var(--orange);border-radius:99px}
.scrim{display:none}
@media (max-width:1000px){.stats{grid-template-columns:repeat(2,1fr)}.grid-2{grid-template-columns:1fr}}
@media (max-width:860px){
.shell{grid-template-columns:1fr}
.sidebar{position:fixed;left:0;top:0;z-index:20;width:var(--sidebar);transform:translateX(-100%);transition:transform .2s}
.shell.open .sidebar{transform:none}
.shell.open + .scrim{display:block;position:fixed;inset:0;background:rgba(10,20,40,.45);z-index:15}
.menu-btn{display:grid;place-items:center;width:38px;height:38px}
.topbar{padding:12px 16px}.content{padding:18px 16px}.profile-name{display:none}}
@media (max-width:520px){.stats{grid-template-columns:1fr}}
</style>
@endverbatim
</head>
<body>
<div class="shell" id="shell">

    {{-- ============ SIDEBAR ============ --}}
    <aside class="sidebar">
        <a href="{{ url('/seller/dashboard') }}" class="brand">SS</a>

        <nav class="nav">
            <a href="{{ url('/seller/dashboard') }}" class="nav-link active">{!! $icon('home') !!} <span>Home</span></a>

            <details class="nav-group">
                <summary class="nav-link">{!! $icon('box') !!} <span>My Products</span>
                    <span class="caret">{!! $icon('chevron', 14) !!}</span>
                </summary>
                <a href="{{ url('/seller/products/create') }}" class="nav-sub">Add</a>
                <a href="{{ url('/seller/products') }}" class="nav-sub">Edit</a>
            </details>

            <a href="{{ url('/seller/orders') }}" class="nav-link">
                {!! $icon('cart') !!} <span>New Orders</span>
                @if($newOrderCount > 0)<span class="badge">{{ $newOrderCount }}</span>@endif
            </a>
            <a href="{{ url('/seller/stock') }}" class="nav-link">{!! $icon('stock') !!} <span>Manage Stock</span></a>
            <a href="{{ url('/seller/reports') }}" class="nav-link">{!! $icon('report') !!} <span>Sales Reports</span></a>
            <a href="{{ url('/seller/profile') }}" class="nav-link">{!! $icon('user') !!} <span>Shop Profile</span></a>
        </nav>

        <div class="side-actions">
            <a href="{{ url('/seller/products/create') }}" class="btn btn-primary">{!! $icon('plus', 16) !!} Add Product</a>
            <a href="{{ url('/seller/orders') }}" class="btn btn-outline">{!! $icon('list', 16) !!} View Orders</a>
        </div>

        <form method="POST" action="{{ $logoutUrl }}" class="logout">
            @csrf
            <button type="submit" class="nav-link">{!! $icon('logout') !!} <span>Log Out</span></button>
        </form>
    </aside>

    {{-- ============ MAIN ============ --}}
    <div class="main">
        <header class="topbar">
            <button class="menu-btn" id="menuBtn" type="button" aria-label="Buka menu">{!! $icon('menu', 22) !!}</button>
            <div class="topbar-right">
                <button class="icon-btn" type="button" aria-label="Notifikasi">{!! $icon('bell') !!}<span class="dot"></span></button>
                <button class="icon-btn" type="button" aria-label="Keranjang">{!! $icon('cart') !!}</button>
                <div class="profile">
                    <span class="avatar">{{ $initial }}</span>
                    <span class="profile-name">{{ $shop }}</span>
                    {!! $icon('chevron', 14) !!}
                </div>
            </div>
        </header>

        <main class="content">
            <div class="page-head">
                <h1>Halo, {{ $shop }}</h1>
                <p>Ada pesanan baru yang perlu Anda proses hari ini.</p>
            </div>

            {{-- Kartu ringkasan --}}
            <section class="stats">
                <article class="stat"><span class="stat-ico">{!! $icon('box', 20) !!}</span>
                    <div><small>New Orders</small><strong>{{ $stats['new_orders'] }}</strong></div></article>
                <article class="stat"><span class="stat-ico">{!! $icon('check', 20) !!}</span>
                    <div><small>Active Products</small><strong>{{ $stats['active_products'] }}</strong></div></article>
                <article class="stat stat-warn"><span class="stat-ico">{!! $icon('alert', 20) !!}</span>
                    <div><small>Low Stock</small><strong>{{ $stats['low_stock'] }}</strong></div></article>
                <article class="stat"><span class="stat-ico">{!! $icon('wallet', 20) !!}</span>
                    <div><small>This Month's Revenue</small><strong>Rp {{ number_format($stats['month_revenue'], 0, ',', '.') }}</strong></div></article>
            </section>

            {{-- Grafik 7 hari + status pesanan --}}
            <section class="grid-2">
                <article class="panel">
                    <h2>Penjualan 7 Hari</h2>
                    <div class="bars bars-lg">
                        @foreach($weekly as $day => $val)
                            <div class="bar-col" title="{{ $val }} pesanan">
                                <div class="bar" style="height: {{ round($val / $weeklyMax * 100) }}%"></div>
                                <span>{{ $day }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="panel">
                    <h2>Status Pesanan</h2>
                    <div class="donut-wrap">
                        <div class="donut" style="background: {{ $donut }}">
                            <div class="donut-hole"><strong>{{ $stats['new_orders'] }}</strong><span>pesanan</span></div>
                        </div>
                        <ul class="legend">
                            @foreach($statusBreakdown as $key => $pct)
                                <li><i class="sw sw-{{ $key }}"></i>{{ $statusLabel[$key] }} {{ $pct }}%</li>
                            @endforeach
                        </ul>
                    </div>
                </article>
            </section>

            {{-- Tabel pesanan --}}
            <section class="panel table-panel">
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr><th>Code</th><th>Buyer</th><th>Product</th><th>Total</th><th>Status</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $o)
                                <tr>
                                    <td class="mono">{{ $o['code'] }}</td>
                                    <td>{{ $o['buyer'] }}</td>
                                    <td>{{ $o['product'] }}</td>
                                    <td>{{ $o['total'] }}</td>
                                    <td><span class="chip chip-{{ $o['status'] }}">{{ ucfirst($o['status']) }}</span></td>
                                    <td><a href="#" class="btn btn-sm btn-outline">Kirim</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="empty">Belum ada pesanan. Pesanan baru akan muncul di sini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Stok + grafik bulanan --}}
            <section class="grid-2">
                <article class="panel">
                    <h2>Manage Stock</h2>
                    <ul class="stock-list">
                        @foreach($lowStockItems as $item)
                            <li>
                                <div class="stock-top"><span>{{ $item['name'] }}</span><b>{{ $item['stock'] }} tersisa</b></div>
                                <div class="meter"><i style="width: {{ round($item['stock'] / $item['max'] * 100) }}%"></i></div>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ url('/seller/stock') }}" class="link">Kelola semua stok</a>
                </article>

                <article class="panel">
                    <h2>Monthly Charts</h2>
                    <div class="bars bars-sm">
                        @foreach($monthly as $m => $val)
                            <div class="bar-col" title="Rp {{ $val }} juta">
                                <div class="bar" style="height: {{ round($val / $monthlyMax * 100) }}%"></div>
                                <span>{{ $m }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>
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
