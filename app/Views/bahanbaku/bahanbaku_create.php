<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Tambah Bahan Baku</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= site_url('bahan-baku') ?>" class="text-brand">Bahan Baku</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tambah</li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('bahan-baku') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <span class="fw-semibold">Form Tambah Bahan Baku</span>
            </div>
            <div class="card-body">

                <form action="<?= site_url('bahan-baku/store') ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <!-- Kode Bahan (auto, read-only preview) -->
                    <div class="mb-3">
                        <label for="kode_bahan" class="form-label fw-semibold">Kode Bahan</label>
                        <input
                            type="text"
                            id="kode_bahan"
                            class="form-control font-monospace"
                            value="<?= esc($kodePreview) ?>"
                            readonly
                            tabindex="-1"
                        >
                        <div class="form-text">Kode dibuat otomatis saat data disimpan.</div>
                    </div>

                    <div class="row">
                        <!-- Nama Bahan -->
                        <div class="col-12 col-md-8 mb-3">
                            <label for="nama_bahan" class="form-label fw-semibold">
                                Nama Bahan <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="nama_bahan"
                                name="nama_bahan"
                                class="form-control <?= session('errors.nama_bahan') ? 'is-invalid' : '' ?>"
                                value="<?= esc(old('nama_bahan')) ?>"
                                placeholder="Contoh: Tepung Terigu"
                                maxlength="100"
                                required
                                autofocus
                            >
                            <?php if (session('errors.nama_bahan')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.nama_bahan')) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Satuan -->
                        <div class="col-12 col-md-4 mb-3">
                            <label for="satuan" class="form-label fw-semibold">
                                Satuan <span class="text-danger">*</span>
                            </label>
                            <select
                                id="satuan"
                                name="satuan"
                                class="form-select <?= session('errors.satuan') ? 'is-invalid' : '' ?>"
                                required
                            >
                                <option value="" disabled <?= old('satuan') ? '' : 'selected' ?>>Pilih satuan</option>
                                <?php foreach ($satuanList as $satuan) : ?>
                                    <option value="<?= esc($satuan) ?>" <?= old('satuan') === $satuan ? 'selected' : '' ?>>
                                        <?= esc($satuan) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.satuan')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.satuan')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Stok awal -->
                        <div class="col-12 col-md-4 mb-3">
                            <label for="stok" class="form-label fw-semibold">Stok Awal</label>
                            <input
                                type="number"
                                id="stok"
                                name="stok"
                                class="form-control <?= session('errors.stok') ? 'is-invalid' : '' ?>"
                                value="<?= esc(old('stok', '0')) ?>"
                                step="0.01"
                                min="0"
                            >
                            <?php if (session('errors.stok')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.stok')) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Stok minimum -->
                        <div class="col-12 col-md-4 mb-3">
                            <label for="stok_minimum" class="form-label fw-semibold">Stok Minimum</label>
                            <input
                                type="number"
                                id="stok_minimum"
                                name="stok_minimum"
                                class="form-control <?= session('errors.stok_minimum') ? 'is-invalid' : '' ?>"
                                value="<?= esc(old('stok_minimum', '0')) ?>"
                                step="0.01"
                                min="0"
                            >
                            <div class="form-text">Batas untuk peringatan stok menipis.</div>
                            <?php if (session('errors.stok_minimum')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.stok_minimum')) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Harga beli -->
                        <div class="col-12 col-md-4 mb-3">
                            <label for="harga_beli" class="form-label fw-semibold">
                                Harga Beli <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input
                                    type="number"
                                    id="harga_beli"
                                    name="harga_beli"
                                    class="form-control <?= session('errors.harga_beli') ? 'is-invalid' : '' ?>"
                                    value="<?= esc(old('harga_beli')) ?>"
                                    step="0.01"
                                    min="0"
                                    required
                                >
                                <?php if (session('errors.harga_beli')) : ?>
                                    <div class="invalid-feedback"><?= esc(session('errors.harga_beli')) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-brand">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <a href="<?= site_url('bahan-baku') ?>" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>