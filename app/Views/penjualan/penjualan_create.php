<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<header class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Tambah Penjualan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="<?= site_url('dashboard') ?>" class="text-brand">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= site_url('penjualan') ?>" class="text-brand">Penjualan</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tambah</li>
            </ol>
        </nav>
    </div>
    <a href="<?= site_url('penjualan') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</header>

<?php if (empty($produks)) : ?>
    <div class="alert alert-warning d-flex gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>
            Tidak ada produk dengan stok tersedia. Tambahkan stok melalui modul
            <a href="<?= site_url('produksi') ?>" class="text-warning text-decoration-underline">Produksi</a> terlebih dahulu.
        </div>
    </div>
<?php else : ?>

<form action="<?= site_url('penjualan/store') ?>" method="post" id="penjualanForm" novalidate>
    <?= csrf_field() ?>

    <div class="row g-4">

        <!-- Left: product picker + cart -->
        <div class="col-12 col-lg-8">

            <!-- Product picker -->
            <section class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <span class="fw-semibold">Pilih Produk</span>
                </div>
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-7">
                            <label for="produkPicker" class="form-label fw-semibold small">Produk</label>
                            <select id="produkPicker" class="form-select">
                                <option value="" disabled selected>Pilih produk untuk ditambahkan…</option>
                                <?php foreach ($produks as $p) : ?>
                                    <option
                                        value="<?= $p['id_produk'] ?>"
                                        data-nama="<?= esc($p['nama_produk']) ?>"
                                        data-kode="<?= esc($p['kode_produk']) ?>"
                                        data-harga="<?= (float) $p['harga'] ?>"
                                        data-stok="<?= (int) $p['stok'] ?>"
                                    >
                                        <?= esc($p['nama_produk']) ?> (<?= esc($p['kode_produk']) ?>) — Rp <?= number_format((float) $p['harga'], 0, ',', '.') ?> · stok <?= (int) $p['stok'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="produkQtyInput" class="form-label fw-semibold small">Qty</label>
                            <input type="number" id="produkQtyInput" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="button" id="addToCartBtn" class="btn btn-brand w-100">
                                <i class="bi bi-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Cart -->
            <section class="card shadow-sm">
                <div class="card-header py-3">
                    <span class="fw-semibold">Keranjang</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end" style="width:110px;">Harga</th>
                                    <th class="text-center" style="width:110px;">Qty</th>
                                    <th class="text-end" style="width:130px;">Subtotal</th>
                                    <th style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="cartTableBody">
                                <tr id="emptyCartRow">
                                    <td colspan="5" class="text-center text-secondary py-4">
                                        <i class="bi bi-cart fs-3 d-block mb-2"></i>
                                        Keranjang masih kosong.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end align-items-center gap-3 py-3">
                    <span class="fw-semibold">Total</span>
                    <span class="fs-5 fw-bold text-brand" id="cartTotalDisplay">Rp 0</span>
                </div>
            </section>

        </div>

        <!-- Right: customer + payment -->
        <div class="col-12 col-lg-4">
            <section class="card shadow-sm">
                <div class="card-header py-3">
                    <span class="fw-semibold">Pembayaran</span>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label for="nama_customer" class="form-label small fw-semibold">Nama Pelanggan</label>
                        <input
                            type="text"
                            id="nama_customer"
                            name="nama_customer"
                            class="form-control <?= session('errors.nama_customer') ? 'is-invalid' : '' ?>"
                            value="<?= esc(old('nama_customer')) ?>"
                            placeholder="Opsional"
                            maxlength="100"
                        >
                        <?php if (session('errors.nama_customer')) : ?>
                            <div class="invalid-feedback"><?= esc(session('errors.nama_customer')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="telepon_customer" class="form-label small fw-semibold">No. Telepon</label>
                        <input
                            type="text"
                            id="telepon_customer"
                            name="telepon_customer"
                            class="form-control <?= session('errors.telepon_customer') ? 'is-invalid' : '' ?>"
                            value="<?= esc(old('telepon_customer')) ?>"
                            placeholder="Opsional"
                            maxlength="20"
                        >
                        <?php if (session('errors.telepon_customer')) : ?>
                            <div class="invalid-feedback"><?= esc(session('errors.telepon_customer')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="metode_bayar" class="form-label small fw-semibold">
                            Metode Bayar <span class="text-danger">*</span>
                        </label>
                        <select
                            id="metode_bayar"
                            name="metode_bayar"
                            class="form-select <?= session('errors.metode_bayar') ? 'is-invalid' : '' ?>"
                            required
                        >
                            <?php foreach ($metodeBayar as $m) : ?>
                                <option value="<?= esc($m) ?>" <?= old('metode_bayar') === $m ? 'selected' : '' ?>>
                                    <?= esc(strtoupper($m)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (session('errors.metode_bayar')) : ?>
                            <div class="invalid-feedback"><?= esc(session('errors.metode_bayar')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="bayar" class="form-label small fw-semibold">
                            Jumlah Bayar <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input
                                type="number"
                                id="bayar"
                                name="bayar"
                                class="form-control <?= session('errors.bayar') ? 'is-invalid' : '' ?>"
                                value="<?= esc(old('bayar')) ?>"
                                step="0.01"
                                min="0"
                                required
                            >
                            <?php if (session('errors.bayar')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.bayar')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 px-1">
                        <span class="text-secondary small">Kembalian</span>
                        <span class="fw-semibold" id="kembalianDisplay">Rp 0</span>
                    </div>

                    <!-- Hidden inputs injected by JS for each cart line -->
                    <div id="hiddenCartInputs"></div>

                    <button type="submit" class="btn btn-brand w-100" id="submitBtn" disabled>
                        <i class="bi bi-save me-1"></i> Simpan Penjualan
                    </button>
                </div>
            </section>
        </div>

    </div>
</form>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const cart = []; // [{ id_produk, nama, kode, harga, stok, qty }]

    const picker        = document.getElementById('produkPicker');
    const qtyInput       = document.getElementById('produkQtyInput');
    const addBtn         = document.getElementById('addToCartBtn');
    const cartBody       = document.getElementById('cartTableBody');
    const emptyRow       = document.getElementById('emptyCartRow');
    const totalDisplay   = document.getElementById('cartTotalDisplay');
    const bayarInput     = document.getElementById('bayar');
    const kembalianDisp  = document.getElementById('kembalianDisplay');
    const hiddenInputs   = document.getElementById('hiddenCartInputs');
    const submitBtn      = document.getElementById('submitBtn');

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    function calcTotal() {
        return cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
    }

    function renderCart() {
        cartBody.innerHTML = '';

        if (cart.length === 0) {
            cartBody.appendChild(emptyRow);
            submitBtn.disabled = true;
        } else {
            cart.forEach((item, idx) => {
                const subtotal = item.harga * item.qty;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div class="fw-semibold">${item.nama}</div>
                        <div class="text-secondary small font-monospace">${item.kode}</div>
                    </td>
                    <td class="text-end">${formatRupiah(item.harga)}</td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm text-center qty-edit"
                               data-idx="${idx}" min="1" max="${item.stok}" value="${item.qty}">
                    </td>
                    <td class="text-end fw-semibold">${formatRupiah(subtotal)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-item" data-idx="${idx}">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                `;
                cartBody.appendChild(row);
            });
            submitBtn.disabled = false;
        }

        const total = calcTotal();
        totalDisplay.textContent = formatRupiah(total);
        updateKembalian();
        renderHiddenInputs();
    }

    function renderHiddenInputs() {
        hiddenInputs.innerHTML = '';
        cart.forEach((item) => {
            hiddenInputs.insertAdjacentHTML('beforeend',
                `<input type="hidden" name="id_produk[]" value="${item.id_produk}">
                 <input type="hidden" name="qty[]" value="${item.qty}">`
            );
        });
    }

    function updateKembalian() {
        const total  = calcTotal();
        const bayar  = parseFloat(bayarInput.value) || 0;
        const kembali = bayar - total;
        kembalianDisp.textContent = formatRupiah(Math.max(kembali, 0));
        kembalianDisp.classList.toggle('text-danger', kembali < 0);
    }

    addBtn.addEventListener('click', function () {
        const option = picker.options[picker.selectedIndex];

        if (!option || !option.value) {
            return;
        }

        const idProduk = option.value;
        const qty      = parseInt(qtyInput.value, 10) || 1;
        const stok     = parseInt(option.dataset.stok, 10);

        const existing = cart.find(i => i.id_produk === idProduk);
        const currentQtyInCart = existing ? existing.qty : 0;

        if (currentQtyInCart + qty > stok) {
            alert(`Stok tidak cukup. Tersedia: ${stok}, sudah di keranjang: ${currentQtyInCart}.`);
            return;
        }

        if (existing) {
            existing.qty += qty;
        } else {
            cart.push({
                id_produk: idProduk,
                nama: option.dataset.nama,
                kode: option.dataset.kode,
                harga: parseFloat(option.dataset.harga),
                stok: stok,
                qty: qty
            });
        }

        picker.selectedIndex = 0;
        qtyInput.value = 1;
        renderCart();
    });

    cartBody.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-item');
        if (!btn) return;
        const idx = parseInt(btn.dataset.idx, 10);
        cart.splice(idx, 1);
        renderCart();
    });

    cartBody.addEventListener('change', function (e) {
        const input = e.target.closest('.qty-edit');
        if (!input) return;
        const idx = parseInt(input.dataset.idx, 10);
        let newQty = parseInt(input.value, 10) || 1;

        if (newQty > cart[idx].stok) {
            alert(`Stok tidak cukup. Maksimal: ${cart[idx].stok}.`);
            newQty = cart[idx].stok;
        }
        if (newQty < 1) newQty = 1;

        cart[idx].qty = newQty;
        renderCart();
    });

    bayarInput.addEventListener('input', updateKembalian);

    document.getElementById('penjualanForm').addEventListener('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Keranjang masih kosong.');
            return;
        }
        const total = calcTotal();
        const bayar = parseFloat(bayarInput.value) || 0;
        if (bayar < total) {
            e.preventDefault();
            alert('Jumlah bayar kurang dari total harga.');
        }
    });

    renderCart();
})();
</script>
<?= $this->endSection() ?>