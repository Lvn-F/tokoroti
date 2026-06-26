<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Produk</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Produk</li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('produk/create') ?>" class="btn btn-brand">
        <i class="bi bi-plus-lg me-1"></i> Tambah Produk
    </a>
</div>

<!-- Card -->
<div class="card shadow-sm">
    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 py-3">
        <span class="fw-semibold">Daftar Produk</span>

        <!-- Search -->
        <form method="get" action="<?= site_url('produk') ?>" class="d-flex gap-2" role="search">
            <div class="input-group input-group-sm" style="min-width:220px;">
                <input
                    type="search"
                    name="q"
                    class="form-control"
                    placeholder="Cari nama / kode…"
                    value="<?= esc($keyword) ?>"
                    aria-label="Cari produk"
                >
                <button class="btn btn-outline-secondary" type="submit" aria-label="Cari">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <?php if ($keyword !== '') : ?>
                <a href="<?= site_url('produk') ?>" class="btn btn-sm btn-outline-secondary" title="Reset pencarian">
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
                        <th style="width:70px;">Gambar</th>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">HPP / Margin</th>
                        <th class="text-end">Stok</th>
                        <th class="text-center">Status</th>
                        <th style="width:120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produks)) : ?>
                        <tr>
                            <td colspan="9" class="text-center text-secondary py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                <?= $keyword !== '' ? 'Tidak ada hasil untuk "' . esc($keyword) . '".' : 'Belum ada produk.' ?>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($produks as $i => $p) : ?>
                            <?php $imgUrl = $model->imageUrl($p['gambar']); ?>
                            <tr>
                                <td class="text-secondary small">
                                    <?= ($pager->getCurrentPage() - 1) * 10 + $i + 1 ?>
                                </td>
                                <td>
                                    <?php if ($imgUrl) : ?>
                                        <img src="<?= esc($imgUrl) ?>"
                                             alt="<?= esc($p['nama_produk']) ?>"
                                             width="48" height="48"
                                             class="rounded object-fit-cover border border-secondary">
                                    <?php else : ?>
                                        <span class="d-inline-flex align-items-center justify-content-center rounded bg-secondary"
                                              style="width:48px;height:48px;">
                                            <i class="bi bi-image text-light"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="font-monospace small"><?= esc($p['kode_produk']) ?></span></td>
                                <td class="fw-semibold"><?= esc($p['nama_produk']) ?></td>
                                <td class="text-secondary small">
                                    <?= $p['nama_kategori'] ? esc($p['nama_kategori']) : '<span class="fst-italic">—</span>' ?>
                                </td>
                                <td class="text-end">Rp <?= number_format((float) $p['harga'], 0, ',', '.') ?></td>
                                <td class="text-end">
                                    <?php
                                    $hpp = $hppMap[(int) $p['id_produk']] ?? null;
                                    ?>
                                    <?php if ($hpp === null) : ?>
                                        <span class="text-secondary small fst-italic">Belum ada resep</span>
                                    <?php else : ?>
                                        <?php
                                        $margin = (float) $p['harga'] - $hpp;
                                        ?>
                                        <div class="small">Rp <?= number_format($hpp, 0, ',', '.') ?></div>
                                        <div class="small <?= $margin >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= $margin >= 0 ? '+' : '' ?>Rp <?= number_format($margin, 0, ',', '.') ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end"><?= (int) $p['stok'] ?></td>
                                <td class="text-center">
                                    <?php if ($p['status'] === 'active') : ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="<?= site_url('produk/edit/' . $p['id_produk']) ?>"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Hapus"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="<?= $p['id_produk'] ?>"
                                                data-nama="<?= esc($p['nama_produk']) ?>">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                        <a href="<?= site_url('produk/resep/' . $p['id_produk']) ?>"
                                            class="btn btn-sm btn-outline-info"
                                            title="Kelola Resep">
                                            <i class="bi bi-list-check"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($pager && $pager->getPageCount() > 1) : ?>
        <div class="card-footer d-flex justify-content-end py-2">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus produk
                <strong id="deleteNama" class="text-danger"></strong>?
                <p class="text-secondary small mt-2 mb-0">
                    Tindakan ini tidak dapat dibatalkan. Gambar produk juga akan dihapus.
                </p>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a id="deleteConfirmBtn" href="#" class="btn btn-danger">
                    <i class="bi bi-trash3 me-1"></i>Ya, Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    const btn  = e.relatedTarget;
    const id   = btn.getAttribute('data-id');
    const nama = btn.getAttribute('data-nama');

    document.getElementById('deleteNama').textContent = nama;
    document.getElementById('deleteConfirmBtn').href  = '<?= site_url('produk/delete/') ?>' + id;
});
</script>
<?= $this->endSection() ?>