<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Edit Produk</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= site_url('produk') ?>" class="text-brand">Produk</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('produk') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <span class="fw-semibold">Form Edit Produk</span>
            </div>
            <div class="card-body">

                <form action="<?= site_url('produk/update/' . $produk['id_produk']) ?>" method="post" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>

                    <div class="row">
                        <!-- Left column: text fields -->
                        <div class="col-12 col-lg-8">

                            <!-- Kode Produk (fixed) -->
                            <div class="mb-3">
                                <label for="kode_produk" class="form-label fw-semibold">Kode Produk</label>
                                <input
                                    type="text"
                                    id="kode_produk"
                                    class="form-control font-monospace"
                                    value="<?= esc($produk['kode_produk']) ?>"
                                    readonly
                                    tabindex="-1"
                                >
                            </div>

                            <div class="row">
                                <!-- Nama Produk -->
                                <div class="col-12 col-md-7 mb-3">
                                    <label for="nama_produk" class="form-label fw-semibold">
                                        Nama Produk <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="nama_produk"
                                        name="nama_produk"
                                        class="form-control <?= session('errors.nama_produk') ? 'is-invalid' : '' ?>"
                                        value="<?= esc(old('nama_produk', $produk['nama_produk'])) ?>"
                                        maxlength="100"
                                        required
                                        autofocus
                                    >
                                    <?php if (session('errors.nama_produk')) : ?>
                                        <div class="invalid-feedback"><?= esc(session('errors.nama_produk')) ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Kategori -->
                                <div class="col-12 col-md-5 mb-3">
                                    <label for="id_kategori" class="form-label fw-semibold">
                                        Kategori <span class="text-danger">*</span>
                                    </label>
                                    <?php $currentKategori = old('id_kategori', $produk['id_kategori']); ?>
                                    <select
                                        id="id_kategori"
                                        name="id_kategori"
                                        class="form-select <?= session('errors.id_kategori') ? 'is-invalid' : '' ?>"
                                        required
                                    >
                                        <?php foreach ($kategoris as $k) : ?>
                                            <option value="<?= $k['id_kategori'] ?>" <?= (int) $currentKategori === (int) $k['id_kategori'] ? 'selected' : '' ?>>
                                                <?= esc($k['nama_kategori']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (session('errors.id_kategori')) : ?>
                                        <div class="invalid-feedback"><?= esc(session('errors.id_kategori')) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                                <textarea
                                    id="deskripsi"
                                    name="deskripsi"
                                    class="form-control <?= session('errors.deskripsi') ? 'is-invalid' : '' ?>"
                                    rows="3"
                                ><?= esc(old('deskripsi', $produk['deskripsi'])) ?></textarea>
                                <?php if (session('errors.deskripsi')) : ?>
                                    <div class="invalid-feedback"><?= esc(session('errors.deskripsi')) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="row">
                                <!-- Harga -->
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="harga" class="form-label fw-semibold">
                                        Harga Jual <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input
                                            type="number"
                                            id="harga"
                                            name="harga"
                                            class="form-control <?= session('errors.harga') ? 'is-invalid' : '' ?>"
                                            value="<?= esc(old('harga', $produk['harga'])) ?>"
                                            step="0.01"
                                            min="0"
                                            required
                                        >
                                        <?php if (session('errors.harga')) : ?>
                                            <div class="invalid-feedback"><?= esc(session('errors.harga')) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Stok -->
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="stok" class="form-label fw-semibold">Stok</label>
                                    <input
                                        type="number"
                                        id="stok"
                                        name="stok"
                                        class="form-control <?= session('errors.stok') ? 'is-invalid' : '' ?>"
                                        value="<?= esc(old('stok', $produk['stok'])) ?>"
                                        min="0"
                                    >
                                    <?php if (session('errors.stok')) : ?>
                                        <div class="invalid-feedback"><?= esc(session('errors.stok')) ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Status -->
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="status" class="form-label fw-semibold">Status</label>
                                    <?php $currentStatus = old('status', $produk['status']); ?>
                                    <select id="status" name="status" class="form-select">
                                        <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="inactive" <?= $currentStatus === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- Right column: current image + replace upload -->
                        <div class="col-12 col-lg-4">
                            <label class="form-label fw-semibold">Gambar Produk</label>

                            <div class="mb-2 d-flex align-items-center justify-content-center border border-secondary rounded"
                                 style="height:180px; background:rgba(255,255,255,.03); overflow:hidden;">
                                <?php $currentImgUrl = $model->imageUrl($produk['gambar']); ?>
                                <img id="imagePreview"
                                     src="<?= $currentImgUrl ? esc($currentImgUrl) : '' ?>"
                                     alt="Gambar produk"
                                     class="<?= $currentImgUrl ? '' : 'd-none' ?> w-100 h-100 object-fit-cover">
                                <span id="imagePlaceholder" class="text-secondary small text-center px-3 <?= $currentImgUrl ? 'd-none' : '' ?>">
                                    <i class="bi bi-image fs-2 d-block mb-1"></i>
                                    Belum ada gambar
                                </span>
                            </div>

                            <input
                                type="file"
                                id="gambar"
                                name="gambar"
                                class="form-control <?= session('errors.gambar') ? 'is-invalid' : '' ?>"
                                accept=".jpg,.jpeg,.png,.webp"
                            >
                            <?php if (session('errors.gambar')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.gambar')) ?></div>
                            <?php endif; ?>
                            <div class="form-text">Kosongkan jika tidak ingin mengganti gambar. Maks. 2MB.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-brand">
                            <i class="bi bi-save me-1"></i> Perbarui
                        </button>
                        <a href="<?= site_url('produk') ?>" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const fileInput   = document.getElementById('gambar');
const preview     = document.getElementById('imagePreview');
const placeholder = document.getElementById('imagePlaceholder');

fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return; // keep showing existing image if user cancels

    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src = e.target.result;
        preview.classList.remove('d-none');
        placeholder.classList.add('d-none');
    };
    reader.readAsDataURL(file);
});
</script>
<?= $this->endSection() ?>