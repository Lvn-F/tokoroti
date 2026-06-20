<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<header class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Detail Produksi</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= site_url('produksi') ?>" class="text-brand">Produksi</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('produksi') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</header>

<div class="row g-4">

    <!-- Info card -->
    <div class="col-12 col-lg-5">
        <section class="card shadow-sm h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold">Informasi Produksi</span>
                <?php
                $badgeClass = match ($produksi['status']) {
                    'selesai' => 'bg-success',
                    'gagal'   => 'bg-danger',
                    default   => 'bg-warning text-dark',
                };
                ?>
                <span class="badge <?= $badgeClass ?> fs-6"><?= esc(ucfirst($produksi['status'])) ?></span>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-secondary">Kode Produksi</dt>
                    <dd class="col-7 font-monospace"><?= esc($produksi['kode_produksi']) ?></dd>

                    <dt class="col-5 text-secondary">Produk</dt>
                    <dd class="col-7 fw-semibold"><?= esc($produksi['nama_produk'] ?? '—') ?></dd>

                    <dt class="col-5 text-secondary">Jumlah</dt>
                    <dd class="col-7"><?= (int) $produksi['jumlah_produksi'] ?> unit</dd>

                    <dt class="col-5 text-secondary">Tanggal Produksi</dt>
                    <dd class="col-7"><?= date('d M Y', strtotime($produksi['tanggal_produksi'])) ?></dd>

                    <dt class="col-5 text-secondary">Operator</dt>
                    <dd class="col-7"><?= esc($produksi['nama_user'] ?? '—') ?></dd>

                    <dt class="col-5 text-secondary">Dibuat</dt>
                    <dd class="col-7 text-secondary"><?= date('d M Y H:i', strtotime($produksi['created_at'])) ?></dd>

                    <?php if (! empty($produksi['catatan'])) : ?>
                        <dt class="col-5 text-secondary">Catatan</dt>
                        <dd class="col-7"><?= esc($produksi['catatan']) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>

            <?php if ($model->isEditable($produksi)) : ?>
                <div class="card-footer d-flex gap-2 py-3">
                    <button type="button" class="btn btn-success flex-fill" data-bs-toggle="modal" data-bs-target="#confirmSelesaiModal">
                        <i class="bi bi-check-circle me-1"></i> Tandai Selesai
                    </button>
                    <button type="button" class="btn btn-outline-danger flex-fill" data-bs-toggle="modal" data-bs-target="#confirmGagalModal">
                        <i class="bi bi-x-circle me-1"></i> Tandai Gagal
                    </button>
                </div>
            <?php else : ?>
                <div class="card-footer py-3">
                    <p class="text-secondary small mb-0">
                        <i class="bi bi-lock-fill me-1"></i>
                        Status sudah final dan tidak dapat diubah lagi.
                    </p>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Resep / bahan baku card -->
    <div class="col-12 col-lg-7">
        <section class="card shadow-sm h-100">
            <div class="card-header py-3">
                <span class="fw-semibold">
                    <?= $produksi['status'] === 'selesai' ? 'Bahan Baku Terpakai' : 'Resep (Estimasi Kebutuhan)' ?>
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Bahan</th>
                                <th class="text-end">
                                    <?= $produksi['status'] === 'selesai' ? 'Jumlah Terpakai' : 'Estimasi Kebutuhan' ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($produksi['status'] === 'selesai') : ?>
                                <?php if (empty($detailProduksi)) : ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-secondary py-4">
                                            Tidak ada data bahan terpakai.
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($detailProduksi as $d) : ?>
                                        <tr>
                                            <td>
                                                <?= esc($d['nama_bahan']) ?>
                                                <span class="text-secondary small font-monospace">(<?= esc($d['kode_bahan']) ?>)</span>
                                            </td>
                                            <td class="text-end">
                                                <?= number_format((float) $d['jumlah_terpakai'], 2) ?> <?= esc($d['satuan']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php else : ?>
                                <?php if (empty($resep)) : ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-secondary py-4">
                                            <i class="bi bi-exclamation-triangle text-warning fs-4 d-block mb-2"></i>
                                            Produk ini belum memiliki resep.
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($resep as $r) : ?>
                                        <?php
                                        $needed    = (float) $r['jumlah_dibutuhkan'] * (int) $produksi['jumlah_produksi'];
                                        $available = (float) $r['stok'];
                                        $isShort   = $available < $needed;
                                        ?>
                                        <tr class="<?= $isShort ? 'table-danger' : '' ?>">
                                            <td>
                                                <?= esc($r['nama_bahan']) ?>
                                                <span class="text-secondary small font-monospace">(<?= esc($r['kode_bahan']) ?>)</span>
                                                <?php if ($isShort) : ?>
                                                    <span class="badge bg-danger ms-1">Stok Kurang</span>
                                                <?php endif; ?>
                                                <div class="text-secondary small">
                                                    Tersedia: <?= number_format($available, 2) ?> <?= esc($r['satuan']) ?>
                                                </div>
                                            </td>
                                            <td class="text-end <?= $isShort ? 'text-danger fw-semibold' : '' ?>">
                                                <?= number_format($needed, 2) ?> <?= esc($r['satuan']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Confirm Selesai Modal -->
<div class="modal fade" id="confirmSelesaiModal" tabindex="-1" aria-labelledby="confirmSelesaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title" id="confirmSelesaiLabel">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>Selesaikan Produksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Menandai produksi ini sebagai <strong>Selesai</strong> akan:
                </p>
                <ul class="small mt-2 mb-0">
                    <li>Mengurangi stok bahan baku sesuai resep</li>
                    <li>Menambah stok produk sebanyak <?= (int) $produksi['jumlah_produksi'] ?> unit</li>
                    <li>Mengunci status produksi (tidak dapat diubah lagi)</li>
                </ul>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= site_url('produksi/update-status/' . $produksi['id_produksi']) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="selesai">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Ya, Selesaikan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Gagal Modal -->
<div class="modal fade" id="confirmGagalModal" tabindex="-1" aria-labelledby="confirmGagalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title" id="confirmGagalLabel">
                    <i class="bi bi-x-circle-fill text-danger me-2"></i>Tandai Produksi Gagal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Yakin ingin menandai produksi <strong><?= esc($produksi['kode_produksi']) ?></strong> sebagai
                    <strong class="text-danger">Gagal</strong>?
                </p>
                <p class="text-secondary small mt-2 mb-0">
                    Karena stok belum pernah dikurangi pada status Proses, tidak ada perubahan stok yang perlu dibatalkan.
                    Tindakan ini akan mengunci status produksi.
                </p>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= site_url('produksi/update-status/' . $produksi['id_produksi']) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="gagal">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i> Ya, Tandai Gagal
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>