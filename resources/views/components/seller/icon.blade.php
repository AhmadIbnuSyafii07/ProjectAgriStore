@props(['name', 'size' => 18])

{{-- Pemakaian: <x-seller.icon name="home" />  (nama beda dengan <x-icon> milik admin, jadi tidak bentrok) --}}
<svg xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24"
     fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true" {{ $attributes->merge(['class' => 'icon']) }}>
    @switch($name)
        @case('home') <path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/> @break
        @case('box') <path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/> @break
        @case('cart') <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.7 11.4a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.8L20 7H6"/> @break
        @case('stock') <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/> @break
        @case('report') <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h6"/> @break
        @case('user') <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/> @break
        @case('plus') <path d="M12 5v14M5 12h14"/> @break
        @case('list') <rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/> @break
        @case('logout') <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/> @break
        @case('bell') <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/> @break
        @case('check') <path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M8.5 12l2.5 2.5L16 9.5"/> @break
        @case('alert') <path d="M12 3l10 18H2L12 3z"/><path d="M12 10v5M12 18h.01"/> @break
        @case('wallet') <rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18"/><path d="M16 15h2"/> @break
        @case('chevron') <path d="M6 9l6 6 6-6"/> @break
        @case('menu') <path d="M4 7h16M4 12h16M4 17h16"/> @break
        @case('edit') <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/> @break
        @case('trash') <path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/> @break
        @case('eye') <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/> @break
        @case('download') <path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M4 21h16"/> @break
        @case('print') <path d="M6 9V3h12v6"/><rect x="4" y="9" width="16" height="8" rx="2"/><path d="M7 14h10v7H7z"/> @break
        @case('search') <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/> @break
        @case('truck') <path d="M2 6h11v10H2z"/><path d="M13 9h4l4 4v3h-8"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/> @break
    @endswitch
</svg>
