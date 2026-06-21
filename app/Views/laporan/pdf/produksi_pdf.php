<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Produksi</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 16px; margin: 0 0 2px 0; }
        .subtitle { font-size: 11px; color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 5px 7px; text-align: left; }
        th { background: #2c2c2c; color: #fff; font-size: 10px; text-transform: uppercase; }
        td.num, th.num { text-align: right; }
        .footer-note { margin-top: 16px; font-size: 9px; color: #888; text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Produksi — Toko Roti</h1>
    <div class="subtitle">
        Periode: <?= date('d M Y', strtotime($startDate)) ?> s.d. <?= date('d M Y', strtotime($endDate)) ?>
        <?php if ($status !== '') : ?>
            &nbsp;|&nbsp; Status: <?= esc(ucfirst($status)) ?>
        <?php endif; ?>
        &nbsp;|&nbsp; Total Data: <?= count($produksis) ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Produk</th>
                <th class="num">Jumlah</th>
                <th>Tanggal</th>
                <th>Operator</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($produksis)) : ?>
                <tr><td colspan="8" style="text-align:center;">Tidak ada data produksi pada periode ini.</td></tr>
            <?php else : ?>
                <?php foreach ($produksis as $i => $p) : ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($p['kode_produksi']) ?></td>
                        <td><?= esc($p['nama_produk'] ?? '-') ?></td>
                        <td class="num"><?= (int) $p['jumlah_produksi'] ?></td>
                        <td><?= date('d-m-Y', strtotime($p['tanggal_produksi'])) ?></td>
                        <td><?= esc($p['nama_user'] ?? '-') ?></td>
                        <td><?= esc(ucfirst($p['status'])) ?></td>
                        <td><?= $p['catatan'] ? esc($p['catatan']) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer-note">Dicetak pada <?= date('d M Y H:i') ?></div>
</body>
</html>