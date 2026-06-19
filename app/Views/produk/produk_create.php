<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Tambah Produk</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= site_url('produk') ?>" class="text-brand">Produk</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tambah</li>
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
                <span class="fw-semibold">Form Tambah Produk</span>
            </div>
            <div class="card-body">

                <form action="<?= site_url('produk/store') ?>" method="post" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>

                    <div class="row">
                        <!-- Left column: text fields -->
                        <div class="col-12 col-lg-8">

                            <!-- Kode Produk (auto, read-only preview) -->
                            <div class="mb-3">
                                <label for="kode_produk" class="form-label fw-semibold">Kode Produk</label>
                                <input
                                    type="text"
                                    id="kode_produk"
                                    class="form-control font-monospace"
                                    value="<?= esc($kodePreview) ?>"
                                    readonly
                                    tabindex="-1"
                                >
                                <div class="form-text">Kode dibuat otomatis saat data disimpan.</div>
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
                                        value="<?= esc(old('nama_produk')) ?>"
                                        placeholder="Contoh: Roti Tawar Gandum"
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
                                    <select
                                        id="id_kategori"
                                        name="id_kategori"
                                        class="form-select <?= session('errors.id_kategori') ? 'is-invalid' : '' ?>"
                                        required
                                    >
                                        <option value="" disabled <?= old('id_kategori') ? '' : 'selected' ?>>Pilih kategori</option>
                                        <?php foreach ($kategoris as $k) : ?>
                                            <option value="<?= $k['id_kategori'] ?>" <?= (int) old('id_kategori') === (int) $k['id_kategori'] ? 'selected' : '' ?>>
                                                <?= esc($k['nama_kategori']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (session('errors.id_kategori')) : ?>
                                        <div class="invalid-feedback"><?= esc(session('errors.id_kategori')) ?></div>
                                    <?php endif; ?>
                                    <?php if (empty($kategoris)) : ?>
                                        <div class="form-text text-warning">
                                            Belum ada kategori. <a href="<?= site_url('kategori/create') ?>" class="text-warning text-decoration-underline">Tambah kategori</a> dahulu.
                                        </div>
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
                                    placeholder="Deskripsi singkat produk (opsional)"
                                ><?= esc(old('deskripsi')) ?></textarea>
                                <?php if (session('errors.deskripsi')) : ?>
                                    <div class="invalid-feedback"><?= esc(session('errors.deskripsi')) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="row">
                                <!-- Harga -->
                                <div class="col-12 col-md-6 mb-3">
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
                                            value="<?= esc(old('harga')) ?>"
                                            step="0.01"
                                            min="0"
                                            required
                                        >
                                        <?php if (session('errors.harga')) : ?>
                                            <div class="invalid-feedback"><?= esc(session('errors.harga')) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Stok awal -->
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="stok" class="form-label fw-semibold">Stok Awal</label>
                                    <input
                                        type="number"
                                        id="stok"
                                        name="stok"
                                        class="form-control <?= session('errors.stok') ? 'is-invalid' : '' ?>"
                                        value="<?= esc(old('stok', '0')) ?>"
                                        min="0"
                                    >
                                    <?php if (session('errors.stok')) : ?>
                                        <div class="invalid-feedback"><?= esc(session('errors.stok')) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>

                        <!-- Right column: image upload + preview -->
                        <div class="col-12 col-lg-4">
                            <label for="gambar" class="form-label fw-semibold">
                                Gambar Produk <span class="text-danger">*</span>
                            </label>

                            <div class="mb-2 d-flex align-items-center justify-content-center border border-secondary rounded"
                                 style="height:180px; background:rgba(255,255,255,.03); overflow:hidden;">
                                <img id="imagePreview"
                                     src=""
                                     alt="Pratinjau gambar"
                                     class="d-none w-100 h-100 object-fit-cover">
                                <span id="imagePlaceholder" class="text-secondary small text-center px-3">
                                    <i class="bi bi-image fs-2 d-block mb-1"></i>
                                    Pratinjau gambar
                                </span>
                            </div>

                            <input
                                type="file"
                                id="gambar"
                                name="gambar"
                                class="form-control <?= session('errors.gambar') ? 'is-invalid' : '' ?>"
                                accept=".jpg,.jpeg,.png,.webp"
                                required
                            >
                            <?php if (session('errors.gambar')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.gambar')) ?></div>
                            <?php endif; ?>
                            <div class="form-text">JPG, PNG, atau WEBP. Maksimal 2MB.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-brand">
                            <i class="bi bi-save me-1"></i> Simpan
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
// Live preview of the chosen image before upload
const fileInput   = document.getElementById('gambar');
const preview     = document.getElementById('imagePreview');
const placeholder = document.getElementById('imagePlaceholder');

fileInput.addEventListener('change', function () {
    const file = this.files[0];

    if (!file) {
        preview.classList.add('d-none');
        placeholder.classList.remove('d-none');
        return;
    }

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