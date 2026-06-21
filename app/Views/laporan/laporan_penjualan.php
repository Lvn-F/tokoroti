<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<header class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Laporan Penjualan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Laporan Penjualan</li>
            </ol>
        </nav>
    </div>
</header>

<!-- Filter -->
<section class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="<?= site_url('laporan/penjualan') ?>" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label for="start_date" class="form-label small fw-semibold">Dari Tanggal</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?= esc($startDate) ?>">
            </div>
            <div class="col-12 col-md-4">
                <label for="end_date" class="form-label small fw-semibold">Sampai Tanggal</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?= esc($endDate) ?>">
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-brand flex-fill">
                    <i class="bi bi-funnel me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Summary + export buttons -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
    <div class="text-secondary small">
        Periode <strong><?= date('d M Y', strtotime($startDate)) ?></strong> s.d.
        <strong><?= date('d M Y', strtotime($endDate)) ?></strong> —
        <?= count($penjualans) ?> transaksi, total
        <strong class="text-brand">Rp <?= number_format($totalOmset, 0, ',', '.') ?></strong>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('laporan/penjualan/export-pdf') . '?' . http_build_query(['start_date' => $startDate, 'end_date' => $endDate]) ?>"
           class="btn btn-outline-danger btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
        <a href="<?= site_url('laporan/penjualan/export-excel') . '?' . http_build_query(['start_date' => $startDate, 'end_date' => $endDate]) ?>"
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
                        <th>No. Invoice</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th>Metode</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Bayar</th>
                        <th class="text-end">Kembalian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($penjualans)) : ?>
                        <tr>
                            <td colspan="9" class="text-center text-secondary py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Tidak ada transaksi pada periode ini.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($penjualans as $i => $p) : ?>
                            <tr>
                                <td class="text-secondary small"><?= $i + 1 ?></td>
                                <td><span class="font-monospace small"><?= esc($p['no_invoice']) ?></span></td>
                                <td class="text-secondary small"><?= date('d M Y H:i', strtotime($p['tanggal_penjualan'])) ?></td>
                                <td><?= $p['nama_customer'] ? esc($p['nama_customer']) : '<span class="fst-italic text-secondary">—</span>' ?></td>
                                <td class="text-secondary small"><?= esc($p['nama_user'] ?? '—') ?></td>
                                <td><span class="badge bg-secondary text-uppercase"><?= esc($p['metode_bayar']) ?></span></td>
                                <td class="text-end fw-semibold">Rp <?= number_format((float) $p['total_harga'], 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format((float) $p['bayar'], 0, ',', '.') ?></td>
                                <td class="text-end text-success">Rp <?= number_format((float) $p['kembalian'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (! empty($penjualans)) : ?>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end fw-semibold">TOTAL</td>
                            <td class="text-end fw-bold text-brand">Rp <?= number_format($totalOmset, 0, ',', '.') ?></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</section>

<?= $this->endSection() ?>