<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<header class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Resep Produk</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= site_url('produk') ?>" class="text-brand">Produk</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($produk['nama_produk']) ?></li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('produk') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</header>

<div class="row g-4">

    <!-- Produk info -->
    <div class="col-12 col-lg-4">
        <section class="card shadow-sm h-100">
            <div class="card-header py-3">
                <span class="fw-semibold">Informasi Produk</span>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-secondary">Kode</dt>
                    <dd class="col-7 font-monospace"><?= esc($produk['kode_produk']) ?></dd>

                    <dt class="col-5 text-secondary">Nama</dt>
                    <dd class="col-7 fw-semibold"><?= esc($produk['nama_produk']) ?></dd>

                    <dt class="col-5 text-secondary">Stok Saat Ini</dt>
                    <dd class="col-7"><?= (int) $produk['stok'] ?> unit</dd>
                </dl>
            </div>
            <div class="card-footer">
                <?php if (empty($resep)) : ?>
                    <p class="text-warning small mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Produk ini belum memiliki resep. Produksi tidak akan mengurangi stok bahan baku sampai resep ditambahkan.
                    </p>
                <?php else : ?>
                    <p class="text-success small mb-0">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Resep aktif dengan <?= count($resep) ?> bahan baku.
                    </p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Recipe list + add form -->
    <div class="col-12 col-lg-8">
        <section class="card shadow-sm">
            <div class="card-header py-3">
                <span class="fw-semibold">Daftar Bahan dalam Resep</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Bahan Baku</th>
                                <th class="text-end">Jumlah Dibutuhkan</th>
                                <th style="width:90px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($resep)) : ?>
                                <tr>
                                    <td colspan="3" class="text-center text-secondary py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        Belum ada bahan dalam resep ini.
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($resep as $r) : ?>
                                    <tr>
                                        <td>
                                            <?= esc($r['nama_bahan']) ?>
                                            <span class="text-secondary small font-monospace">(<?= esc($r['kode_bahan']) ?>)</span>
                                        </td>
                                        <td class="text-end">
                                            <?= number_format((float) $r['jumlah_dibutuhkan'], 2) ?> <?= esc($r['satuan']) ?>
                                            <span class="text-secondary small d-block">per 1 unit produk</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteResepModal"
                                                    data-id="<?= $r['id_resep'] ?>"
                                                    data-nama="<?= esc($r['nama_bahan']) ?>">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer py-3">
                <?php if (empty($availableBahan)) : ?>
                    <p class="text-secondary small mb-0">
                        <?= empty($resep)
                            ? 'Belum ada data bahan baku. Tambahkan bahan baku terlebih dahulu di menu Bahan Baku.'
                            : 'Semua bahan baku yang tersedia sudah masuk ke dalam resep ini.' ?>
                    </p>
                <?php else : ?>
                    <form action="<?= site_url('produk/resep/' . $produk['id_produk'] . '/store') ?>" method="post" class="row g-2" novalidate>
                        <?= csrf_field() ?>

                        <div class="col-12 col-md-6">
                            <label for="id_bahan" class="form-label fw-semibold small">
                                Bahan Baku <span class="text-danger">*</span>
                            </label>
                            <select id="id_bahan" name="id_bahan" class="form-select" required>
                                <option value="" disabled selected>Pilih bahan baku</option>
                                <?php foreach ($availableBahan as $b) : ?>
                                    <option value="<?= $b['id_bahan'] ?>" data-satuan="<?= esc($b['satuan']) ?>">
                                        <?= esc($b['nama_bahan']) ?> (<?= esc($b['kode_bahan']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="jumlah_dibutuhkan" class="form-label fw-semibold small">
                                Jumlah per Unit Produk <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    id="jumlah_dibutuhkan"
                                    name="jumlah_dibutuhkan"
                                    class="form-control"
                                    step="0.01"
                                    min="0.01"
                                    placeholder="0.00"
                                    required
                                >
                                <span class="input-group-text" id="satuanLabel">satuan</span>
                            </div>
                        </div>

                        <div class="col-12 col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-brand w-100">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteResepModal" tabindex="-1" aria-labelledby="deleteResepLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title" id="deleteResepLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus <strong id="deleteResepNama" class="text-danger"></strong> dari resep ini?
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a id="deleteResepConfirmBtn" href="#" class="btn btn-danger">
                    <i class="bi bi-trash3 me-1"></i>Ya, Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Show the selected bahan's unit next to the quantity input
const bahanSelect  = document.getElementById('id_bahan');
const satuanLabel  = document.getElementById('satuanLabel');

if (bahanSelect) {
    bahanSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        satuanLabel.textContent = selected.dataset.satuan || 'satuan';
    });
}

// Populate delete modal
document.getElementById('deleteResepModal').addEventListener('show.bs.modal', function (e) {
    const btn  = e.relatedTarget;
    const id   = btn.getAttribute('data-id');
    const nama = btn.getAttribute('data-nama');

    document.getElementById('deleteResepNama').textContent = nama;
    document.getElementById('deleteResepConfirmBtn').href =
        '<?= site_url('produk/resep/' . $produk['id_produk'] . '/delete/') ?>' + id;
});
</script>
<?= $this->endSection() ?>