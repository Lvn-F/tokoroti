<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Bahan Baku</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Bahan Baku</li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('bahan-baku/create') ?>" class="btn btn-brand">
        <i class="bi bi-plus-lg me-1"></i> Tambah Bahan
    </a>
</div>

<!-- Card -->
<div class="card shadow-sm">
    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 py-3">
        <span class="fw-semibold">Daftar Bahan Baku</span>

        <!-- Search -->
        <form method="get" action="<?= site_url('bahan-baku') ?>" class="d-flex gap-2" role="search">
            <div class="input-group input-group-sm" style="min-width:220px;">
                <input
                    type="search"
                    name="q"
                    class="form-control"
                    placeholder="Cari nama / kode…"
                    value="<?= esc($keyword) ?>"
                    aria-label="Cari bahan baku"
                >
                <button class="btn btn-outline-secondary" type="submit" aria-label="Cari">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <?php if ($keyword !== '') : ?>
                <a href="<?= site_url('bahan-baku') ?>" class="btn btn-sm btn-outline-secondary" title="Reset pencarian">
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
                        <th>Kode</th>
                        <th>Nama Bahan</th>
                        <th>Satuan</th>
                        <th class="text-end">Stok</th>
                        <th class="text-end">Stok Min.</th>
                        <th class="text-end">Harga Beli</th>
                        <th style="width:120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bahans)) : ?>
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                <?= $keyword !== '' ? 'Tidak ada hasil untuk "' . esc($keyword) . '".' : 'Belum ada bahan baku.' ?>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($bahans as $i => $b) : ?>
                            <?php $isLow = $model->isLowStock($b); ?>
                            <tr class="<?= $isLow ? 'table-danger' : '' ?>">
                                <td class="text-secondary small">
                                    <?= ($pager->getCurrentPage() - 1) * 10 + $i + 1 ?>
                                </td>
                                <td>
                                    <span class="font-monospace small"><?= esc($b['kode_bahan']) ?></span>
                                </td>
                                <td class="fw-semibold">
                                    <?= esc($b['nama_bahan']) ?>
                                    <?php if ($isLow) : ?>
                                        <span class="badge bg-danger ms-1" title="Stok di bawah/sama dengan minimum">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Stok Menipis
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($b['satuan']) ?></td>
                                <td class="text-end <?= $isLow ? 'text-danger fw-semibold' : '' ?>">
                                    <?= number_format((float) $b['stok'], 2) ?>
                                </td>
                                <td class="text-end text-secondary small">
                                    <?= number_format((float) $b['stok_minimum'], 2) ?>
                                </td>
                                <td class="text-end">
                                    Rp <?= number_format((float) $b['harga_beli'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="<?= site_url('bahan-baku/edit/' . $b['id_bahan']) ?>"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Hapus"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="<?= $b['id_bahan'] ?>"
                                                data-nama="<?= esc($b['nama_bahan']) ?>">
                                            <i class="bi bi-trash3"></i>
                                        </button>
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
                Yakin ingin menghapus bahan baku
                <strong id="deleteNama" class="text-danger"></strong>?
                <p class="text-secondary small mt-2 mb-0">
                    Tindakan ini tidak dapat dibatalkan.
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
    document.getElementById('deleteConfirmBtn').href  = '<?= site_url('bahan-baku/delete/') ?>' + id;
});
</script>
<?= $this->endSection() ?>