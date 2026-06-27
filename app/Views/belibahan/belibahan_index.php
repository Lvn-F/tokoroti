<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<header class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Pembelian Bahan Baku</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Pembelian Bahan Baku</li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('beli-bahan/create') ?>" class="btn btn-brand">
        <i class="bi bi-plus-lg me-1"></i> Tambah Pembelian
    </a>
</header>

<section class="card shadow-sm">
    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 py-3">
        <span class="fw-semibold">Riwayat Pembelian</span>

        <form method="get" action="<?= site_url('beli-bahan') ?>" class="d-flex gap-2" role="search">
            <div class="input-group input-group-sm" style="min-width:220px;">
                <input
                    type="search"
                    name="q"
                    class="form-control"
                    placeholder="Cari kode beli / nama bahan…"
                    value="<?= esc($keyword) ?>"
                    aria-label="Cari pembelian"
                >
                <button class="btn btn-outline-secondary" type="submit" aria-label="Cari">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <?php if ($keyword !== '') : ?>
                <a href="<?= site_url('beli-bahan') ?>" class="btn btn-sm btn-outline-secondary" title="Reset pencarian">
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
                        <th>Kode Beli</th>
                        <th>Bahan Baku</th>
                        <th>Tanggal</th>
                        <th class="text-end">Jumlah Beli</th>
                        <th class="text-end">Konversi</th>
                        <th class="text-end">Total Harga</th>
                        <th class="text-end">Harga/Satuan</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pembelian)) : ?>
                        <tr>
                            <td colspan="9" class="text-center text-secondary py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                <?= $keyword !== '' ? 'Tidak ada hasil untuk "' . esc($keyword) . '".' : 'Belum ada data pembelian.' ?>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($pembelian as $i => $p) : ?>
                            <tr>
                                <td class="text-secondary small">
                                    <?= ($pager->getCurrentPage() - 1) * 10 + $i + 1 ?>
                                </td>
                                <td><span class="font-monospace small"><?= esc($p['kode_beli']) ?></span></td>
                                <td class="fw-semibold">
                                    <?= esc($p['nama_bahan'] ?? '—') ?>
                                    <span class="text-secondary small font-monospace">(<?= esc($p['kode_bahan'] ?? '') ?>)</span>
                                </td>
                                <td class="text-secondary small"><?= date('d M Y', strtotime($p['tanggal_beli'])) ?></td>
                                <td class="text-end">
                                    <?= number_format((float) $p['jumlah_beli'], 2) ?> <?= esc($p['satuan_beli']) ?>
                                </td>
                                <td class="text-end text-secondary small">
                                    <?= number_format((float) $p['jumlah_konversi'], 2) ?> <?= esc($p['satuan_dasar'] ?? '') ?>
                                </td>
                                <td class="text-end fw-semibold">
                                    Rp <?= number_format((float) $p['total_harga'], 0, ',', '.') ?>
                                </td>
                                <td class="text-end text-success">
                                    Rp <?= number_format((float) $p['harga_per_satuan'], 2, ',', '.') ?> / <?= esc($p['satuan_dasar'] ?? '') ?>
                                </td>
                                <td class="text-secondary small"><?= esc($p['nama_user'] ?? '—') ?></td>
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