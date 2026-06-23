<nav class="nav flex-column">
    <a class="nav-link {{ request()->routeIs('beranda') ? 'active' : '' }}" href="{{ route('beranda') }}">
        <i class="bi bi-house-door"></i> Beranda
    </a>
    <a class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" href="{{ route('produk.index') }}">
        <i class="bi bi-box-seam"></i> Produk
    </a>
    <a class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
        <i class="bi bi-clipboard-data"></i> Inventory
    </a>
    <a class="nav-link {{ request()->routeIs('pembelian.*') ? 'active' : '' }}" href="{{ route('pembelian.index') }}">
        <i class="bi bi-bag-plus"></i> Pembelian
    </a>
    <a class="nav-link {{ request()->routeIs('riwayat.admin') ? 'active' : '' }}" href="{{ route('riwayat.admin') }}">
        <i class="bi bi-receipt"></i> Penjualan
    </a>
    <a class="nav-link {{ request()->routeIs('utang-piutang.*') ? 'active' : '' }}" href="{{ route('utang-piutang.index') }}">
        <i class="bi bi-cash-stack"></i> Utang Piutang
    </a>
    <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
        <i class="bi bi-graph-up"></i> Laporan
    </a>
</nav>
