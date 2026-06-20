<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Edit Bahan Baku</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= site_url('bahan-baku') ?>" class="text-brand">Bahan Baku</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                <span class="fw-semibold">Form Edit Bahan Baku</span>
            </div>
            <div class="card-body">

                <form action="<?= site_url('bahan-baku/update/' . $bahan['id_bahan']) ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <!-- Kode Bahan (fixed, never changes) -->
                    <div class="mb-3">
                        <label for="kode_bahan" class="form-label fw-semibold">Kode Bahan</label>
                        <input
                            type="text"
                            id="kode_bahan"
                            class="form-control font-monospace"
                            value="<?= esc($bahan['kode_bahan']) ?>"
                            readonly
                            tabindex="-1"
                        >
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
                                value="<?= esc(old('nama_bahan', $bahan['nama_bahan'])) ?>"
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
                                <?php $currentSatuan = old('satuan', $bahan['satuan']); ?>
                                <?php foreach ($satuanList as $satuan) : ?>
                                    <option value="<?= esc($satuan) ?>" <?= $currentSatuan === $satuan ? 'selected' : '' ?>>
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
                        <!-- Stok -->
                        <div class="col-12 col-md-4 mb-3">
                            <label for="stok" class="form-label fw-semibold">Stok</label>
                            <input
                                type="number"
                                id="stok"
                                name="stok"
                                class="form-control <?= session('errors.stok') ? 'is-invalid' : '' ?>"
                                value="<?= esc(old('stok', $bahan['stok'])) ?>"
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
                                value="<?= esc(old('stok_minimum', $bahan['stok_minimum'])) ?>"
                                step="0.01"
                                min="0"
                            >
                            <?php if (session('errors.stok_minimum')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.stok_minimum')) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Harga beli -->
                        <div class="col-12 col-md-4 mb-3">
                            <label for="harga_beli" class="form-label fw-semibold">
                                Harga Beli/Satuan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input
                                    type="number"
                                    id="harga_beli"
                                    name="harga_beli"
                                    class="form-control <?= session('errors.harga_beli') ? 'is-invalid' : '' ?>"
                                    value="<?= esc(old('harga_beli', $bahan['harga_beli'])) ?>"
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
                            <i class="bi bi-save me-1"></i> Perbarui
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