<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<header class="mb-4">
    <h4 class="mb-0 fw-semibold">Dashboard</h4>
    <p class="text-secondary small mb-0">Ringkasan aktivitas Toko Roti hari ini.</p>
</header>

<!-- Stat cards -->
<div class="row g-3 mb-4">

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                     style="width:48px;height:48px;">
                    <i class="bi bi-basket fs-5"></i>
                </div>
                <div>
                    <div class="text-secondary small">Total Produk Aktif</div>
                    <div class="fs-4 fw-bold"><?= $totalProduk ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger"
                     style="width:48px;height:48px;">
                    <i class="bi bi-exclamation-triangle fs-5"></i>
                </div>
                <div>
                    <div class="text-secondary small">Bahan Stok Menipis</div>
                    <div class="fs-4 fw-bold"><?= $totalLowStock ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success"
                     style="width:48px;height:48px;">
                    <i class="bi bi-cart-check fs-5"></i>
                </div>
                <div>
                    <div class="text-secondary small">Transaksi Hari Ini</div>
                    <div class="fs-4 fw-bold"><?= $totalPenjualanHariIni ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning"
                     style="width:48px;height:48px;">
                    <i class="bi bi-cash-coin fs-5"></i>
                </div>
                <div>
                    <div class="text-secondary small">Omset Hari Ini</div>
                    <div class="fs-5 fw-bold">Rp <?= number_format($omsetHariIni, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row g-4">

    <!-- Revenue chart -->
    <div class="col-12 col-lg-8">
        <section class="card shadow-sm h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold">Omset 7 Hari Terakhir</span>
                <span class="text-secondary small">Bulan ini: Rp <?= number_format($omsetBulanIni, 0, ',', '.') ?></span>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="110"></canvas>
            </div>
        </section>
    </div>

    <!-- Produksi status breakdown -->
    <div class="col-12 col-lg-4">
        <section class="card shadow-sm h-100">
            <div class="card-header py-3">
                <span class="fw-semibold">Status Produksi</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center">
                <canvas id="produksiChart" height="180"></canvas>
                <ul class="list-unstyled w-100 mt-3 small mb-0">
                    <li class="d-flex justify-content-between border-bottom border-secondary py-1">
                        <span><i class="bi bi-circle-fill text-warning me-2"></i>Proses</span>
                        <span class="fw-semibold"><?= $produksiByStatus['proses'] ?></span>
                    </li>
                    <li class="d-flex justify-content-between border-bottom border-secondary py-1">
                        <span><i class="bi bi-circle-fill text-success me-2"></i>Selesai</span>
                        <span class="fw-semibold"><?= $produksiByStatus['selesai'] ?></span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span><i class="bi bi-circle-fill text-danger me-2"></i>Gagal</span>
                        <span class="fw-semibold"><?= $produksiByStatus['gagal'] ?></span>
                    </li>
                </ul>
            </div>
        </section>
    </div>

    <!-- Low stock warning -->
    <div class="col-12 col-lg-6">
        <section class="card shadow-sm h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold">Bahan Baku Stok Menipis</span>
                <a href="<?= site_url('bahan-baku') ?>" class="small text-brand">Lihat semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Bahan</th>
                                <th class="text-end">Stok</th>
                                <th class="text-end">Min.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($lowStockItems)) : ?>
                                <tr>
                                    <td colspan="3" class="text-center text-secondary py-4">
                                        <i class="bi bi-check-circle text-success fs-4 d-block mb-2"></i>
                                        Semua stok bahan baku aman.
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($lowStockItems as $b) : ?>
                                    <tr class="table-danger">
                                        <td>
                                            <?= esc($b['nama_bahan']) ?>
                                            <span class="text-secondary small font-monospace">(<?= esc($b['kode_bahan']) ?>)</span>
                                        </td>
                                        <td class="text-end text-danger fw-semibold"><?= number_format((float) $b['stok'], 2) ?> <?= esc($b['satuan']) ?></td>
                                        <td class="text-end text-secondary small"><?= number_format((float) $b['stok_minimum'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Recent activity -->
    <div class="col-12 col-lg-6">
        <section class="card shadow-sm h-100">
            <div class="card-header py-3">
                <span class="fw-semibold">Riwayat Aktivitas Terbaru</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($recentLogs)) : ?>
                        <li class="list-group-item text-center text-secondary py-4">
                            Belum ada aktivitas.
                        </li>
                    <?php else : ?>
                        <?php foreach ($recentLogs as $log) : ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="small fw-semibold"><?= esc($log['aktivitas']) ?></div>
                                    <div class="text-secondary small">
                                        <span class="badge bg-secondary"><?= esc($log['modul']) ?></span>
                                        <?= esc($log['nama_user'] ?? 'Unknown') ?>
                                    </div>
                                </div>
                                <span class="text-secondary small text-nowrap"><?= date('d M, H:i', strtotime($log['created_at'])) ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </section>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
(function () {
    function getThemeColors() {
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        return {
            grid: isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.08)',
            text: isDark ? '#c7c7c7' : '#555'
        };
    }

    let colors = getThemeColors();

    const revenueChart = new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($revenueChart['labels']) ?>,
            datasets: [{
                label: 'Omset',
                data: <?= json_encode($revenueChart['values']) ?>,
                borderColor: '#dd4814',
                backgroundColor: 'rgba(221,72,20,.15)',
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: '#dd4814'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: colors.text }, grid: { color: colors.grid } },
                y: {
                    ticks: { color: colors.text, callback: (val) => 'Rp ' + (val / 1000) + 'k' },
                    grid: { color: colors.grid }
                }
            }
        }
    });

    const produksiChart = new Chart(document.getElementById('produksiChart'), {
        type: 'doughnut',
        data: {
            labels: ['Proses', 'Selesai', 'Gagal'],
            datasets: [{
                data: [
                    <?= $produksiByStatus['proses'] ?>,
                    <?= $produksiByStatus['selesai'] ?>,
                    <?= $produksiByStatus['gagal'] ?>
                ],
                backgroundColor: ['#ffc107', '#198754', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            cutout: '65%'
        }
    });

    // Re-theme both charts whenever the user toggles dark/light mode.
    document.addEventListener('themechange', function () {
        colors = getThemeColors();

        revenueChart.options.scales.x.ticks.color = colors.text;
        revenueChart.options.scales.x.grid.color  = colors.grid;
        revenueChart.options.scales.y.ticks.color = colors.text;
        revenueChart.options.scales.y.grid.color  = colors.grid;
        revenueChart.update();

        // Doughnut has no axes, but update() keeps it consistent if you
        // add theme-dependent options later.
        produksiChart.update();
    });
})();
</script>
<?= $this->endSection() ?>