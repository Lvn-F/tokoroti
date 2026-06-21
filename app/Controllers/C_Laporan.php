<?php

namespace App\Controllers;

use App\Models\M_Penjualan;
use App\Models\M_Produksi;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class C_Laporan extends BaseController
{
    protected M_Penjualan $penjualanModel;
    protected M_Produksi $produksiModel;

    public function __construct()
    {
        $this->penjualanModel = new M_Penjualan();
        $this->produksiModel  = new M_Produksi();
    }

    // ==================================================================
    // LAPORAN PENJUALAN
    // ==================================================================

    public function penjualan(): string
    {
        [$start, $end] = $this->resolveDateRange();

        $data = [
            'pageTitle'  => 'Laporan Penjualan',
            'startDate'  => $start,
            'endDate'    => $end,
            'penjualans' => $this->penjualanModel->getByDateRange($start, $end),
            'totalOmset' => $this->penjualanModel->sumByDateRange($start, $end),
        ];

        return view('laporan/laporan_penjualan', $data);
    }

    public function exportPenjualanPdf()
    {
        [$start, $end] = $this->resolveDateRange();

        $data = [
            'startDate'  => $start,
            'endDate'    => $end,
            'penjualans' => $this->penjualanModel->getByDateRange($start, $end),
            'totalOmset' => $this->penjualanModel->sumByDateRange($start, $end),
        ];

        $html = view('laporan/pdf/penjualan_pdf', $data);

        $this->logActivity("Mengekspor laporan penjualan PDF ({$start} s.d. {$end})", 'Laporan');

        return $this->streamPdf($html, "laporan-penjualan-{$start}_to_{$end}.pdf");
    }

    public function exportPenjualanExcel()
    {
        [$start, $end] = $this->resolveDateRange();

        $rows = $this->penjualanModel->getByDateRange($start, $end);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Penjualan');

        $headers = ['No', 'No. Invoice', 'Tanggal', 'Pelanggan', 'Kasir', 'Metode Bayar', 'Total Harga', 'Bayar', 'Kembalian'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $rowNum = 2;
        foreach ($rows as $i => $r) {
            $sheet->fromArray([
                $i + 1,
                $r['no_invoice'],
                date('d-m-Y H:i', strtotime($r['tanggal_penjualan'])),
                $r['nama_customer'] ?: '-',
                $r['nama_user'] ?? '-',
                strtoupper($r['metode_bayar']),
                (float) $r['total_harga'],
                (float) $r['bayar'],
                (float) $r['kembalian'],
            ], null, "A{$rowNum}");
            $rowNum++;
        }

        // Total row
        $sheet->setCellValue("F{$rowNum}", 'TOTAL');
        $sheet->getStyle("F{$rowNum}")->getFont()->setBold(true);
        $sheet->setCellValue("G{$rowNum}", array_sum(array_column($rows, 'total_harga')));
        $sheet->getStyle("G{$rowNum}")->getFont()->setBold(true);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->logActivity("Mengekspor laporan penjualan Excel ({$start} s.d. {$end})", 'Laporan');

        return $this->streamExcel($spreadsheet, "laporan-penjualan-{$start}_to_{$end}.xlsx");
    }

    // ==================================================================
    // LAPORAN PRODUKSI
    // ==================================================================

    public function produksi(): string
    {
        [$start, $end] = $this->resolveDateRange();
        $status = $this->request->getGet('status') ?? '';

        $data = [
            'pageTitle'  => 'Laporan Produksi',
            'startDate'  => $start,
            'endDate'    => $end,
            'status'     => $status,
            'statusList' => M_Produksi::STATUS,
            'produksis'  => $this->produksiModel->getByDateRange($start, $end, $status),
        ];

        return view('laporan/laporan_produksi', $data);
    }

    public function exportProduksiPdf()
    {
        [$start, $end] = $this->resolveDateRange();
        $status = $this->request->getGet('status') ?? '';

        $data = [
            'startDate' => $start,
            'endDate'   => $end,
            'status'    => $status,
            'produksis' => $this->produksiModel->getByDateRange($start, $end, $status),
        ];

        $html = view('laporan/pdf/produksi_pdf', $data);

        $this->logActivity("Mengekspor laporan produksi PDF ({$start} s.d. {$end})", 'Laporan');

        return $this->streamPdf($html, "laporan-produksi-{$start}_to_{$end}.pdf");
    }

    public function exportProduksiExcel()
    {
        [$start, $end] = $this->resolveDateRange();
        $status = $this->request->getGet('status') ?? '';

        $rows = $this->produksiModel->getByDateRange($start, $end, $status);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Produksi');

        $headers = ['No', 'Kode Produksi', 'Produk', 'Jumlah', 'Tanggal', 'Operator', 'Status', 'Catatan'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $rowNum = 2;
        foreach ($rows as $i => $r) {
            $sheet->fromArray([
                $i + 1,
                $r['kode_produksi'],
                $r['nama_produk'] ?? '-',
                (int) $r['jumlah_produksi'],
                date('d-m-Y', strtotime($r['tanggal_produksi'])),
                $r['nama_user'] ?? '-',
                ucfirst($r['status']),
                $r['catatan'] ?: '-',
            ], null, "A{$rowNum}");
            $rowNum++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->logActivity("Mengekspor laporan produksi Excel ({$start} s.d. {$end})", 'Laporan');

        return $this->streamExcel($spreadsheet, "laporan-produksi-{$start}_to_{$end}.xlsx");
    }

    // ==================================================================
    // Helpers
    // ==================================================================

    /**
     * Resolve start/end date from query params, defaulting to the
     * current calendar month if not provided or invalid.
     *
     * @return array{0: string, 1: string}
     */
    protected function resolveDateRange(): array
    {
        $start = $this->request->getGet('start_date');
        $end   = $this->request->getGet('end_date');

        $defaultStart = date('Y-m-01');
        $defaultEnd   = date('Y-m-d');

        $isValid = static fn ($d) => $d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) !== false;

        $start = $isValid($start) ? $start : $defaultStart;
        $end   = $isValid($end) ? $end : $defaultEnd;

        // Guard against a reversed range.
        if (strtotime($start) > strtotime($end)) {
            [$start, $end] = [$end, $start];
        }

        return [$start, $end];
    }

    /**
     * Render HTML to a PDF and stream it as a download.
     */
    protected function streamPdf(string $html, string $filename)
    {
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    /**
     * Write a Spreadsheet to xlsx bytes and stream it as a download.
     */
    protected function streamExcel(Spreadsheet $spreadsheet, string $filename)
    {
        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }
}