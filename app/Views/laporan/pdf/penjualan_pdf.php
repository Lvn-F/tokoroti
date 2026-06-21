<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 16px; margin: 0 0 2px 0; }
        .subtitle { font-size: 11px; color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 5px 7px; text-align: left; }
        th { background: #2c2c2c; color: #fff; font-size: 10px; text-transform: uppercase; }
        td.num, th.num { text-align: right; }
        tfoot td { font-weight: bold; background: #f4f4f4; }
        .footer-note { margin-top: 16px; font-size: 9px; color: #888; text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan — Toko Roti</h1>
    <div class="subtitle">
        Periode: <?= date('d M Y', strtotime($startDate)) ?> s.d. <?= date('d M Y', strtotime($endDate)) ?>
        &nbsp;|&nbsp; Total Transaksi: <?= count($penjualans) ?>
        &nbsp;|&nbsp; Total Omset: Rp <?= number_format($totalOmset, 0, ',', '.') ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th class="num">Total</th>
                <th class="num">Bayar</th>
                <th class="num">Kembalian</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($penjualans)) : ?>
                <tr><td colspan="9" style="text-align:center;">Tidak ada transaksi pada periode ini.</td></tr>
            <?php else : ?>
                <?php foreach ($penjualans as $i => $p) : ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($p['no_invoice']) ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($p['tanggal_penjualan'])) ?></td>
                        <td><?= $p['nama_customer'] ? esc($p['nama_customer']) : '-' ?></td>
                        <td><?= esc($p['nama_user'] ?? '-') ?></td>
                        <td><?= esc(strtoupper($p['metode_bayar'])) ?></td>
                        <td class="num">Rp <?= number_format((float) $p['total_harga'], 0, ',', '.') ?></td>
                        <td class="num">Rp <?= number_format((float) $p['bayar'], 0, ',', '.') ?></td>
                        <td class="num">Rp <?= number_format((float) $p['kembalian'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <?php if (! empty($penjualans)) : ?>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align:right;">TOTAL</td>
                    <td class="num">Rp <?= number_format($totalOmset, 0, ',', '.') ?></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        <?php endif; ?>
    </table>

    <div class="footer-note">Dicetak pada <?= date('d M Y H:i') ?></div>
</body>
</html>