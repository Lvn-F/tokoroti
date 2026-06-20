<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<header class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Tambah Produksi</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= site_url('produksi') ?>" class="text-brand">Produksi</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tambah</li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('produksi') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</header>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <section class="card shadow-sm">
            <div class="card-header py-3">
                <span class="fw-semibold">Form Tambah Produksi</span>
            </div>
            <div class="card-body">

                <div class="alert alert-info d-flex gap-2 small" role="alert">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                        Produksi akan dibuat dengan status <strong>Proses</strong>.
                        Stok bahan baku &amp; produk baru akan berubah setelah Anda
                        menandai produksi ini sebagai <strong>Selesai</strong> dari halaman detail.
                    </div>
                </div>

                <form action="<?= site_url('produksi/store') ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="kode_produksi" class="form-label fw-semibold">Kode Produksi</label>
                        <input
                            type="text"
                            id="kode_produksi"
                            class="form-control font-monospace"
                            value="<?= esc($kodePreview) ?>"
                            readonly
                            tabindex="-1"
                        >
                        <div class="form-text">Kode dibuat otomatis saat data disimpan.</div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-8 mb-3">
                            <label for="id_produk" class="form-label fw-semibold">
                                Produk <span class="text-danger">*</span>
                            </label>
                            <select
                                id="id_produk"
                                name="id_produk"
                                class="form-select <?= session('errors.id_produk') ? 'is-invalid' : '' ?>"
                                required
                            >
                                <option value="" disabled <?= old('id_produk') ? '' : 'selected' ?>>Pilih produk</option>
                                <?php foreach ($produks as $p) : ?>
                                    <option value="<?= $p['id_produk'] ?>" <?= (int) old('id_produk') === (int) $p['id_produk'] ? 'selected' : '' ?>>
                                        <?= esc($p['nama_produk']) ?> (<?= esc($p['kode_produk']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.id_produk')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.id_produk')) ?></div>
                            <?php endif; ?>
                            <?php if (empty($produks)) : ?>
                                <div class="form-text text-warning">
                                    Belum ada produk aktif. <a href="<?= site_url('produk/create') ?>" class="text-warning text-decoration-underline">Tambah produk</a> dahulu.
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12 col-md-4 mb-3">
                            <label for="jumlah_produksi" class="form-label fw-semibold">
                                Jumlah <span class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                id="jumlah_produksi"
                                name="jumlah_produksi"
                                class="form-control <?= session('errors.jumlah_produksi') ? 'is-invalid' : '' ?>"
                                value="<?= esc(old('jumlah_produksi', '1')) ?>"
                                min="1"
                                required
                            >
                            <?php if (session('errors.jumlah_produksi')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.jumlah_produksi')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_produksi" class="form-label fw-semibold">
                            Tanggal Produksi <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            id="tanggal_produksi"
                            name="tanggal_produksi"
                            class="form-control <?= session('errors.tanggal_produksi') ? 'is-invalid' : '' ?>"
                            value="<?= esc(old('tanggal_produksi', date('Y-m-d'))) ?>"
                            required
                        >
                        <?php if (session('errors.tanggal_produksi')) : ?>
                            <div class="invalid-feedback"><?= esc(session('errors.tanggal_produksi')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="catatan" class="form-label fw-semibold">Catatan</label>
                        <textarea
                            id="catatan"
                            name="catatan"
                            class="form-control <?= session('errors.catatan') ? 'is-invalid' : '' ?>"
                            rows="2"
                            placeholder="Catatan tambahan (opsional)"
                            maxlength="255"
                        ><?= esc(old('catatan')) ?></textarea>
                        <?php if (session('errors.catatan')) : ?>
                            <div class="invalid-feedback"><?= esc(session('errors.catatan')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-brand">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <a href="<?= site_url('produksi') ?>" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </section>
    </div>
</div>

<?= $this->endSection() ?>