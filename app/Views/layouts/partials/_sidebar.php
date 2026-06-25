<?php
$role        = session()->get('role');
$currentUri  = service('uri')->getPath();

/**
 * Helper: returns 'active' if the current URI starts with $prefix.
 */
function sidebarActive(string $prefix, string $currentUri): string
{
    return str_starts_with(ltrim($currentUri, '/'), ltrim($prefix, '/')) ? 'active' : '';
}
?>

<!-- ===================== SIDEBAR ===================== -->
<nav id="sidebar" class="d-flex flex-column flex-shrink-0 p-0"
     style="width:260px; min-height:100vh; background:#1a1d23; position:fixed; top:0; left:0; z-index:1030; transition:transform .3s ease;">

    <!-- Brand -->
    <a href="<?= site_url('dashboard') ?>"
       class="d-flex align-items-center gap-2 px-4 py-3 text-decoration-none border-bottom border-secondary">
        <span class="fs-5 fw-bold text-white">🍞 Toko Roti</span>
    </a>

    <!-- Nav links -->
    <ul class="nav nav-pills flex-column mb-auto px-2 pt-3 gap-1">

        <!-- Dashboard — all roles -->
        <li class="nav-item">
            <a href="<?= site_url('dashboard') ?>"
               class="nav-link text-white <?= sidebarActive('dashboard', $currentUri) ?>"
               aria-current="<?= sidebarActive('dashboard', $currentUri) === 'active' ? 'page' : 'false' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <?php if (in_array($role, ['admin', 'kasir', 'produser'], true)) : ?>
        <!-- MASTER DATA -->
        <li class="nav-item mt-2">
            <span class="nav-link text-uppercase text-secondary small py-1 ps-2" style="font-size:.7rem;letter-spacing:.08em;">
                Master Data
            </span>
        </li>
        <?php endif; ?>

        <?php if ($role === 'admin') : ?>
        <li class="nav-item">
            <a href="<?= site_url('kategori') ?>"
               class="nav-link text-white <?= sidebarActive('kategori', $currentUri) ?>">
                <i class="bi bi-tags me-2"></i> Kategori Produk
            </a>
        </li>
        <?php endif; ?>

        <?php if (in_array($role, ['admin', 'produser'], true)) : ?>
        <li class="nav-item">
            <a href="<?= site_url('bahan-baku') ?>"
               class="nav-link text-white <?= sidebarActive('bahan-baku', $currentUri) ?>">
                <i class="bi bi-box-seam me-2"></i> Bahan Baku
            </a>
        </li>
        <?php endif; ?>

        <?php if ($role === 'admin') : ?>
        <li class="nav-item">
            <a href="<?= site_url('produk') ?>"
               class="nav-link text-white <?= sidebarActive('produk', $currentUri) ?>">
                <i class="bi bi-basket me-2"></i> Produk
            </a>
        </li>
        <?php endif; ?>

        <!-- TRANSAKSI -->
        <?php if (in_array($role, ['admin', 'kasir', 'produser'], true)) : ?>
        <li class="nav-item mt-2">
            <span class="nav-link text-uppercase text-secondary small py-1 ps-2" style="font-size:.7rem;letter-spacing:.08em;">
                Transaksi
            </span>
        </li>
        <?php endif; ?>

        <?php if (in_array($role, ['admin', 'produser'], true)) : ?>
        <li class="nav-item">
            <a href="<?= site_url('produksi') ?>"
               class="nav-link text-white <?= sidebarActive('produksi', $currentUri) ?>">
                <i class="bi bi-gear me-2"></i> Produksi
            </a>
        </li>
        <?php endif; ?>

        <?php if (in_array($role, ['admin', 'kasir'], true)) : ?>
        <li class="nav-item">
            <a href="<?= site_url('penjualan') ?>"
               class="nav-link text-white <?= sidebarActive('penjualan', $currentUri) ?>">
                <i class="bi bi-cart3 me-2"></i> Penjualan
            </a>
        </li>
        <?php endif; ?>

        <!-- LAPORAN -->
        <?php if ($role === 'admin') : ?>
        <li class="nav-item mt-2">
            <span class="nav-link text-uppercase text-secondary small py-1 ps-2" style="font-size:.7rem;letter-spacing:.08em;">
                Laporan
            </span>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('laporan/penjualan') ?>"
               class="nav-link text-white <?= sidebarActive('laporan/penjualan', $currentUri) ?>">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Lap. Penjualan
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('laporan/produksi') ?>"
               class="nav-link text-white <?= sidebarActive('laporan/produksi', $currentUri) ?>">
                <i class="bi bi-file-earmark-text me-2"></i> Lap. Produksi
            </a>
        </li>
        <?php endif; ?>

        <!-- PENGATURAN -->
        <?php if ($role === 'admin') : ?>
        <li class="nav-item mt-2">
            <!-- <span class="nav-link text-uppercase text-secondary small py-1 ps-2" style="font-size:.7rem;letter-spacing:.08em;">
                Pengaturan
            </span> -->
        </li>
        <li class="nav-item">
            <a href="<?= site_url('users') ?>"
               class="nav-link text-white <?= sidebarActive('users', $currentUri) ?>">
                <!-- <i class="bi bi-people me-2"></i> Manajemen User -->
            <!-- </a> -->
        <!-- </li> -->
        <?php endif; ?>

    </ul>

    <!-- Version footer -->
    <div class="px-4 py-3 border-top border-secondary">
        <small class="text-secondary">v1.0.0</small>
    </div>

</nav>
<!-- =================== END SIDEBAR =================== -->