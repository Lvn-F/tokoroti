<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<header class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Laporan Produksi</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Laporan Produksi</li>
            </ol>
        </nav>
    </div>
</header>

<!-- Filter -->
<section class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="<?= site_url('laporan/produksi') ?>" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label for="start_date" class="form-label small fw-semibold">Dari Tanggal</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?= esc($startDate) ?>">
            </div>
            <div class="col-12 col-md-3">
                <label for="end_date" class="form-label small fw-semibold">Sampai Tanggal</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?= esc($endDate) ?>">
            </div>
            <div class="col-12 col-md-3">
                <label for="status" class="form-label small fw-semibold">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <?php foreach ($statusList as $s) : ?>
                        <option value="<?= esc($s) ?>" <?= $status === $s ? 'selected' : '' ?>>
                            <?= esc(ucfirst($s)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-brand w-100">
                    <i class="bi bi-funnel me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Summary + export buttons -->
<?php
$exportParams = ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $status];
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
    <div class="text-secondary small">
        Periode <strong><?= date('d M Y', strtotime($startDate)) ?></strong> s.d.
        <strong><?= date('d M Y', strtotime($endDate)) ?></strong> —
        <?= count($produksis) ?> data produksi
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('laporan/produksi/export-pdf') . '?' . http_build_query($exportParams) ?>"
           class="btn btn-outline-danger btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
        <a href="<?= site_url('laporan/produksi/export-excel') . '?' . http_build_query($exportParams) ?>"
           class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
    </div>
</div>

<!-- Table -->
<section class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Kode</th>
                        <th>Produk</th>
                        <th class="text-end">Jumlah</th>
                        <th>Tanggal</th>
                        <th>Operator</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produksis)) : ?>
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Tidak ada data produksi pada periode ini.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($produksis as $i => $p) : ?>
                            <tr>
                                <td class="text-secondary small"><?= $i + 1 ?></td>
                                <td><span class="font-monospace small"><?= esc($p['kode_produksi']) ?></span></td>
                                <td class="fw-semibold"><?= esc($p['nama_produk'] ?? '—') ?></td>
                                <td class="text-end"><?= (int) $p['jumlah_produksi'] ?></td>
                                <td class="text-secondary small"><?= date('d M Y', strtotime($p['tanggal_produksi'])) ?></td>
                                <td class="text-secondary small"><?= esc($p['nama_user'] ?? '—') ?></td>
                                <td class="text-center">
                                    <?php
                                    $badgeClass = match ($p['status']) {
                                        'selesai' => 'bg-success',
                                        'gagal'   => 'bg-danger',
                                        default   => 'bg-warning text-dark',
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst($p['status'])) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?= $this->endSection() ?>