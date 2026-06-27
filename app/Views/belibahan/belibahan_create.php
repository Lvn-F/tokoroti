<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<header class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Tambah Pembelian Bahan Baku</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= site_url('beli-bahan') ?>" class="text-brand">Pembelian Bahan Baku</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tambah</li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('beli-bahan') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</header>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <section class="card shadow-sm">
            <div class="card-header py-3">
                <span class="fw-semibold">Form Pembelian</span>
            </div>
            <div class="card-body">

                <div class="alert alert-info small d-flex gap-2" role="alert">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                        Stok bahan baku hanya bertambah lewat pembelian di sini.
                        Harga beli per satuan dasar akan dihitung otomatis dan
                        menggantikan harga sebelumnya (harga terbaru yang dipakai).
                    </div>
                </div>

                <form action="<?= site_url('beli-bahan/store') ?>" method="post" id="beliForm" novalidate>
                    <?= csrf_field() ?>

                    <!-- Mode toggle -->
                    <fieldset class="mb-4">
                        <legend class="form-label fw-semibold small text-uppercase text-secondary" style="font-size:.7rem;letter-spacing:.06em;">
                            Jenis Pembelian
                        </legend>
                        <div class="btn-group w-100" role="group" aria-label="Pilih jenis pembelian">
                            <input type="radio" class="btn-check" name="mode" id="modeExisting" value="existing" checked>
                            <label class="btn btn-outline-brand" for="modeExisting">
                                <i class="bi bi-box-seam me-1"></i> Bahan Sudah Ada
                            </label>

                            <input type="radio" class="btn-check" name="mode" id="modeNew" value="new">
                            <label class="btn btn-outline-brand" for="modeNew">
                                <i class="bi bi-plus-square me-1"></i> Bahan Baru
                            </label>
                        </div>
                    </fieldset>

                    <!-- EXISTING bahan picker -->
                    <div id="existingPanel">
                        <div class="mb-3">
                            <label for="id_bahan" class="form-label fw-semibold">
                                Bahan Baku <span class="text-danger">*</span>
                            </label>
                            <select id="id_bahan" name="id_bahan" class="form-select <?= session('errors.id_bahan') ? 'is-invalid' : '' ?>">
                                <option value="" disabled selected>Pilih bahan baku</option>
                                <?php foreach ($bahans as $b) : ?>
                                    <option value="<?= $b['id_bahan'] ?>" data-satuan-dasar="<?= esc($b['satuan']) ?>">
                                        <?= esc($b['nama_bahan']) ?> (<?= esc($b['kode_bahan']) ?>) — satuan dasar: <?= esc($b['satuan']) ?>, stok saat ini: <?= number_format((float) $b['stok'], 2) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.id_bahan')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.id_bahan')) ?></div>
                            <?php endif; ?>
                            <?php if (empty($bahans)) : ?>
                                <div class="form-text text-warning">
                                    Belum ada bahan baku terdaftar. Gunakan opsi "Bahan Baru" di atas.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- NEW bahan registration -->
                    <div id="newPanel" class="d-none">
                        <div class="row">
                            <div class="col-12 col-md-7 mb-3">
                                <label for="nama_bahan_baru" class="form-label fw-semibold">
                                    Nama Bahan Baru <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_bahan_baru"
                                    name="nama_bahan_baru"
                                    class="form-control <?= session('errors.nama_bahan_baru') ? 'is-invalid' : '' ?>"
                                    placeholder="Contoh: Tepung Terigu"
                                    maxlength="100"
                                >
                                <?php if (session('errors.nama_bahan_baru')) : ?>
                                    <div class="invalid-feedback"><?= esc(session('errors.nama_bahan_baru')) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-12 col-md-5 mb-3">
                                <label for="satuan_dasar_baru" class="form-label fw-semibold">
                                    Satuan Dasar (Penyimpanan) <span class="text-danger">*</span>
                                </label>
                                <select id="satuan_dasar_baru" name="satuan_dasar_baru" class="form-select <?= session('errors.satuan_dasar_baru') ? 'is-invalid' : '' ?>">
                                    <option value="" disabled selected>Pilih satuan dasar</option>
                                    <?php foreach ($satuanDasar as $s) : ?>
                                        <option value="<?= esc($s) ?>"><?= esc($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.satuan_dasar_baru')) : ?>
                                    <div class="invalid-feedback"><?= esc(session('errors.satuan_dasar_baru')) ?></div>
                                <?php endif; ?>
                                <div class="form-text">Stok akan selalu disimpan dalam satuan ini.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="stok_minimum_baru" class="form-label fw-semibold">Stok Minimum</label>
                            <input
                                type="number"
                                id="stok_minimum_baru"
                                name="stok_minimum_baru"
                                class="form-control"
                                step="0.01"
                                min="0"
                                value="0"
                            >
                            <div class="form-text">Batas untuk peringatan stok menipis.</div>
                        </div>
                    </div>

                    <hr class="border-secondary">

                    <!-- Purchase details (shared by both modes) -->
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="satuan_beli" class="form-label fw-semibold">
                                Satuan Beli <span class="text-danger">*</span>
                            </label>
                            <select id="satuan_beli" name="satuan_beli" class="form-select <?= session('errors.satuan_beli') ? 'is-invalid' : '' ?>" required>
                                <option value="" disabled selected>Pilih bahan/satuan dasar dahulu</option>
                            </select>
                            <?php if (session('errors.satuan_beli')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.satuan_beli')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="tanggal_beli" class="form-label fw-semibold">
                                Tanggal Beli <span class="text-danger">*</span>
                            </label>
                            <input
                                type="date"
                                id="tanggal_beli"
                                name="tanggal_beli"
                                class="form-control <?= session('errors.tanggal_beli') ? 'is-invalid' : '' ?>"
                                value="<?= esc(old('tanggal_beli', date('Y-m-d'))) ?>"
                                required
                            >
                            <?php if (session('errors.tanggal_beli')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.tanggal_beli')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="jumlah_beli" class="form-label fw-semibold">
                                Jumlah Beli <span class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                id="jumlah_beli"
                                name="jumlah_beli"
                                class="form-control <?= session('errors.jumlah_beli') ? 'is-invalid' : '' ?>"
                                step="0.01"
                                min="0.01"
                                required
                            >
                            <?php if (session('errors.jumlah_beli')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.jumlah_beli')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="total_harga" class="form-label fw-semibold">
                                Total Harga <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input
                                    type="number"
                                    id="total_harga"
                                    name="total_harga"
                                    class="form-control <?= session('errors.total_harga') ? 'is-invalid' : '' ?>"
                                    step="0.01"
                                    min="0.01"
                                    required
                                >
                            </div>
                            <?php if (session('errors.total_harga')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.total_harga')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Live conversion preview -->
                    <div class="alert alert-secondary small mb-4" id="previewBox">
                        <i class="bi bi-calculator me-1"></i>
                        Isi jumlah &amp; harga untuk melihat estimasi harga per satuan dasar.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-brand">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <a href="<?= site_url('beli-bahan') ?>" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </section>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    // Conversion table mirrored from M_BeliBahan::CONVERSION (PHP -> JS)
    const CONVERSION = <?= json_encode($conversion) ?>;

    const modeExisting   = document.getElementById('modeExisting');
    const modeNew         = document.getElementById('modeNew');
    const existingPanel   = document.getElementById('existingPanel');
    const newPanel         = document.getElementById('newPanel');
    const idBahanSelect    = document.getElementById('id_bahan');
    const satuanDasarBaru  = document.getElementById('satuan_dasar_baru');
    const satuanBeliSelect = document.getElementById('satuan_beli');
    const jumlahBeliInput  = document.getElementById('jumlah_beli');
    const totalHargaInput  = document.getElementById('total_harga');
    const previewBox       = document.getElementById('previewBox');

    function unitsCompatibleWith(baseUnit) {
        return Object.keys(CONVERSION).filter(k => CONVERSION[k].base === baseUnit);
    }

    function populateSatuanBeli(baseUnit) {
        satuanBeliSelect.innerHTML = '';

        if (!baseUnit) {
            satuanBeliSelect.innerHTML = '<option value="" disabled selected>Pilih bahan/satuan dasar dahulu</option>';
            return;
        }

        const compatible = unitsCompatibleWith(baseUnit);
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.disabled = true;
        placeholder.selected = true;
        placeholder.textContent = 'Pilih satuan beli';
        satuanBeliSelect.appendChild(placeholder);

        compatible.forEach(unit => {
            const opt = document.createElement('option');
            opt.value = unit;
            opt.textContent = unit;
            satuanBeliSelect.appendChild(opt);
        });

        updatePreview();
    }

    function toggleMode() {
        const isNew = modeNew.checked;
        newPanel.classList.toggle('d-none', !isNew);
        existingPanel.classList.toggle('d-none', isNew);

        if (isNew) {
            populateSatuanBeli(satuanDasarBaru.value || null);
        } else {
            const selected = idBahanSelect.options[idBahanSelect.selectedIndex];
            populateSatuanBeli(selected ? selected.dataset.satuanDasar : null);
        }
    }

    function updatePreview() {
        const satuanBeli = satuanBeliSelect.value;
        const jumlah = parseFloat(jumlahBeliInput.value) || 0;
        const total = parseFloat(totalHargaInput.value) || 0;

        if (!satuanBeli || jumlah <= 0 || total <= 0) {
            previewBox.innerHTML = '<i class="bi bi-calculator me-1"></i> Isi jumlah &amp; harga untuk melihat estimasi harga per satuan dasar.';
            return;
        }

        const conv = CONVERSION[satuanBeli];
        const jumlahKonversi = jumlah * conv.factor;
        const hargaPerSatuan = jumlahKonversi > 0 ? total / jumlahKonversi : 0;

        previewBox.innerHTML = `
            <i class="bi bi-calculator me-1"></i>
            <strong>${jumlah} ${satuanBeli}</strong> = <strong>${jumlahKonversi.toLocaleString('id-ID')} ${conv.base}</strong>
            &nbsp;→&nbsp; harga per ${conv.base}:
            <strong class="text-brand">Rp ${hargaPerSatuan.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 4})}</strong>
        `;
    }

    modeExisting.addEventListener('change', toggleMode);
    modeNew.addEventListener('change', toggleMode);

    idBahanSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        populateSatuanBeli(selected ? selected.dataset.satuanDasar : null);
    });

    satuanDasarBaru.addEventListener('change', function () {
        populateSatuanBeli(this.value);
    });

    satuanBeliSelect.addEventListener('change', updatePreview);
    jumlahBeliInput.addEventListener('input', updatePreview);
    totalHargaInput.addEventListener('input', updatePreview);

    // Initial state
    toggleMode();
})();
</script>
<?= $this->endSection() ?>