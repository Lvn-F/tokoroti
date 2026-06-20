<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<header class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Penjualan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Penjualan</li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('penjualan/create') ?>" class="btn btn-brand">
        <i class="bi bi-plus-lg me-1"></i> Tambah Penjualan
    </a>
</header>

<section class="card shadow-sm">
    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 py-3">
        <span class="fw-semibold">Daftar Penjualan</span>

        <form method="get" action="<?= site_url('penjualan') ?>" class="d-flex flex-wrap gap-2" role="search">
            <div class="input-group input-group-sm" style="min-width:200px;">
                <input
                    type="search"
                    name="q"
                    class="form-control"
                    placeholder="Cari kode / invoice / pelanggan…"
                    value="<?= esc($keyword) ?>"
                    aria-label="Cari penjualan"
                >
                <button class="btn btn-outline-secondary" type="submit" aria-label="Cari">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            <select name="status" class="form-select form-select-sm" style="min-width:140px;"
                    onchange="this.form.submit()" aria-label="Filter status">
                <option value="">Semua Status</option>
                <?php foreach ($statusList as $s) : ?>
                    <option value="<?= esc($s) ?>" <?= $status === $s ? 'selected' : '' ?>>
                        <?= esc(ucfirst($s)) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($keyword !== '' || $status !== '') : ?>
                <a href="<?= site_url('penjualan') ?>" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>No. Invoice</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th class="text-end">Total</th>
                        <th>Metode</th>
                        <th>Tanggal</th>
                        <th class="text-center">Status</th>
                        <th style="width:90px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($penjualans)) : ?>
                        <tr>
                            <td colspan="9" class="text-center text-secondary py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                <?= ($keyword !== '' || $status !== '') ? 'Tidak ada hasil yang cocok.' : 'Belum ada data penjualan.' ?>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($penjualans as $i => $p) : ?>
                            <tr>
                                <td class="text-secondary small">
                                    <?= ($pager->getCurrentPage() - 1) * 10 + $i + 1 ?>
                                </td>
                                <td><span class="font-monospace small"><?= esc($p['no_invoice']) ?></span></td>
                                <td><?= $p['nama_customer'] ? esc($p['nama_customer']) : '<span class="text-secondary fst-italic">—</span>' ?></td>
                                <td class="text-secondary small"><?= esc($p['nama_user'] ?? '—') ?></td>
                                <td class="text-end fw-semibold">Rp <?= number_format((float) $p['total_harga'], 0, ',', '.') ?></td>
                                <td><span class="badge bg-secondary text-uppercase"><?= esc($p['metode_bayar']) ?></span></td>
                                <td class="text-secondary small"><?= date('d M Y H:i', strtotime($p['tanggal_penjualan'])) ?></td>
                                <td class="text-center">
                                    <?php
                                    $badgeClass = match ($p['status']) {
                                        'completed' => 'bg-success',
                                        'cancel'    => 'bg-danger',
                                        default     => 'bg-warning text-dark',
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst($p['status'])) ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('penjualan/detail/' . $p['id_penjualan']) ?>"
                                       class="btn btn-sm btn-outline-info"
                                       title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($pager && $pager->getPageCount() > 1) : ?>
        <div class="card-footer d-flex justify-content-end py-2">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>