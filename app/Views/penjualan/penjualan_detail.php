<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<header class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Detail Penjualan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= site_url('penjualan') ?>" class="text-brand">Penjualan</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('penjualan') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</header>

<div class="row g-4">

    <!-- Transaction info -->
    <div class="col-12 col-lg-5">
        <section class="card shadow-sm h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold">Informasi Transaksi</span>
                <?php
                $badgeClass = match ($penjualan['status']) {
                    'completed' => 'bg-success',
                    'cancel'    => 'bg-danger',
                    default     => 'bg-warning text-dark',
                };
                ?>
                <span class="badge <?= $badgeClass ?> fs-6"><?= esc(ucfirst($penjualan['status'])) ?></span>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-secondary">No. Invoice</dt>
                    <dd class="col-7 font-monospace"><?= esc($penjualan['no_invoice']) ?></dd>

                    <dt class="col-5 text-secondary">Kode Penjualan</dt>
                    <dd class="col-7 font-monospace"><?= esc($penjualan['kode_penjualan']) ?></dd>

                    <dt class="col-5 text-secondary">Tanggal</dt>
                    <dd class="col-7"><?= date('d M Y H:i', strtotime($penjualan['tanggal_penjualan'])) ?></dd>

                    <dt class="col-5 text-secondary">Kasir</dt>
                    <dd class="col-7"><?= esc($penjualan['nama_user'] ?? '—') ?></dd>

                    <dt class="col-5 text-secondary">Pelanggan</dt>
                    <dd class="col-7"><?= $penjualan['nama_customer'] ? esc($penjualan['nama_customer']) : '<span class="fst-italic">—</span>' ?></dd>

                    <?php if (! empty($penjualan['telepon_customer'])) : ?>
                        <dt class="col-5 text-secondary">No. Telepon</dt>
                        <dd class="col-7"><?= esc($penjualan['telepon_customer']) ?></dd>
                    <?php endif; ?>

                    <dt class="col-5 text-secondary">Metode Bayar</dt>
                    <dd class="col-7"><span class="badge bg-secondary text-uppercase"><?= esc($penjualan['metode_bayar']) ?></span></dd>
                </dl>
            </div>
            <div class="card-footer">
                <dl class="row mb-0">
                    <dt class="col-6">Total Harga</dt>
                    <dd class="col-6 text-end fw-semibold">Rp <?= number_format((float) $penjualan['total_harga'], 0, ',', '.') ?></dd>

                    <dt class="col-6">Bayar</dt>
                    <dd class="col-6 text-end">Rp <?= number_format((float) $penjualan['bayar'], 0, ',', '.') ?></dd>

                    <dt class="col-6">Kembalian</dt>
                    <dd class="col-6 text-end text-success fw-semibold">Rp <?= number_format((float) $penjualan['kembalian'], 0, ',', '.') ?></dd>
                </dl>
            </div>
        </section>
    </div>

    <!-- Line items -->
    <div class="col-12 col-lg-7">
        <section class="card shadow-sm">
            <div class="card-header py-3">
                <span class="fw-semibold">Item Pembelian</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Harga</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($detailPenjualan)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">
                                        Tidak ada item.
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($detailPenjualan as $d) : ?>
                                    <tr>
                                        <td>
                                            <?= esc($d['nama_produk'] ?? '—') ?>
                                            <span class="text-secondary small font-monospace">(<?= esc($d['kode_produk'] ?? '') ?>)</span>
                                        </td>
                                        <td class="text-end">Rp <?= number_format((float) $d['harga'], 0, ',', '.') ?></td>
                                        <td class="text-center"><?= (int) $d['qty'] ?></td>
                                        <td class="text-end fw-semibold">Rp <?= number_format((float) $d['subtotal'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-semibold">Total</td>
                                <td class="text-end fw-bold text-brand">Rp <?= number_format((float) $penjualan['total_harga'], 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>
    </div>

</div>

<?= $this->endSection() ?>