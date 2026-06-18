<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Edit Kategori</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= site_url('kategori') ?>" class="text-brand">Kategori Produk</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('kategori') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <span class="fw-semibold">Form Edit Kategori</span>
            </div>
            <div class="card-body">

                <form action="<?= site_url('kategori/update/' . $kategori['id_kategori']) ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <!-- Nama Kategori -->
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label fw-semibold">
                            Nama Kategori <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="nama_kategori"
                            name="nama_kategori"
                            class="form-control <?= session('errors.nama_kategori') ? 'is-invalid' : '' ?>"
                            value="<?= esc(old('nama_kategori', $kategori['nama_kategori'])) ?>"
                            placeholder="Contoh: Roti Tawar"
                            maxlength="100"
                            required
                            autofocus
                        >
                        <?php if (session('errors.nama_kategori')) : ?>
                            <div class="invalid-feedback">
                                <?= esc(session('errors.nama_kategori')) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                        <textarea
                            id="deskripsi"
                            name="deskripsi"
                            class="form-control <?= session('errors.deskripsi') ? 'is-invalid' : '' ?>"
                            rows="3"
                            placeholder="Deskripsi singkat (opsional)"
                            maxlength="255"
                        ><?= esc(old('deskripsi', $kategori['deskripsi'])) ?></textarea>
                        <?php if (session('errors.deskripsi')) : ?>
                            <div class="invalid-feedback">
                                <?= esc(session('errors.deskripsi')) ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">Maksimal 255 karakter.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-brand">
                            <i class="bi bi-save me-1"></i> Perbarui
                        </button>
                        <a href="<?= site_url('kategori') ?>" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>