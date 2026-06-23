<nav class="nav flex-column">
    <a class="nav-link {{ request()->routeIs('penjualan.*') ? 'active' : '' }}" href="{{ route('penjualan.index') }}">
        <i class="bi bi-cart-plus"></i> Penjualan
    </a>
    <a class="nav-link {{ request()->routeIs('riwayat.kasir') ? 'active' : '' }}" href="{{ route('riwayat.kasir') }}">
        <i class="bi bi-clock-history"></i> Riwayat Penjualan
    </a>
    <a class="nav-link {{ request()->routeIs('piutang.kasir') ? 'active' : '' }}" href="{{ route('piutang.kasir') }}">
        <i class="bi bi-person-lines-fill"></i> Piutang
    </a>
</nav>
