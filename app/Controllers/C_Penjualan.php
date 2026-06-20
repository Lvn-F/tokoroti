<?php

namespace App\Controllers;

use App\Models\M_DetailPenjualan;
use App\Models\M_Penjualan;
use App\Models\M_Produk;

class C_Penjualan extends BaseController
{
    protected M_Penjualan $model;
    protected M_DetailPenjualan $detailModel;
    protected M_Produk $produkModel;

    public function __construct()
    {
        $this->model       = new M_Penjualan();
        $this->detailModel = new M_DetailPenjualan();
        $this->produkModel = new M_Produk();
    }

    // ------------------------------------------------------------------
    // INDEX — list with search + status filter + pagination
    // ------------------------------------------------------------------
    public function index(): string
    {
        $keyword = $this->request->getGet('q') ?? '';
        $status  = $this->request->getGet('status') ?? '';
        $perPage = 10;

        $data = [
            'pageTitle'  => 'Penjualan',
            'keyword'    => $keyword,
            'status'     => $status,
            'statusList' => M_Penjualan::STATUS,
            'penjualans' => $this->model->search($keyword, $status, $perPage),
            'pager'      => $this->model->pager,
        ];

        return view('penjualan/penjualan_index', $data);
    }

    // ------------------------------------------------------------------
    // CREATE — POS-style form: pick products, qty, payment
    // ------------------------------------------------------------------
    public function create(): string
    {
        return view('penjualan/penjualan_create', [
            'pageTitle'    => 'Tambah Penjualan',
            'produks'      => $this->produkModel
                ->where('status', 'active')
                ->where('stok >', 0)
                ->orderBy('nama_produk', 'ASC')
                ->findAll(),
            'metodeBayar'  => M_Penjualan::METODE_BAYAR,
        ]);
    }

    // ------------------------------------------------------------------
    // STORE — validate stock, then atomically: deduct stok, insert
    // penjualan + detail_penjualan, status = completed.
    // ------------------------------------------------------------------
    public function store()
    {
        $rules = [
            'nama_customer'    => 'permit_empty|max_length[100]',
            'telepon_customer' => 'permit_empty|max_length[20]',
            'metode_bayar'     => 'required|in_list[cash,debit,credit,qris]',
            'bayar'            => 'required|decimal',
            'id_produk'        => 'required',
            'qty'              => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $idProdukList = (array) $this->request->getPost('id_produk');
        $qtyList      = (array) $this->request->getPost('qty');

        if (empty($idProdukList) || count($idProdukList) !== count($qtyList)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data produk pada keranjang tidak valid.');
        }

        // --- Step 1: build line items + validate stock BEFORE touching anything ---
        $lineItems = [];
        $shortages = [];
        $totalHarga = 0;

        foreach ($idProdukList as $i => $idProduk) {
            $qty = (int) $qtyList[$i];

            if ($qty <= 0) {
                continue;
            }

            $produk = $this->produkModel->find($idProduk);

            if ($produk === null) {
                continue;
            }

            if ((int) $produk['stok'] < $qty) {
                $shortages[] = sprintf(
                    '%s (diminta %d, stok tersedia %d)',
                    $produk['nama_produk'],
                    $qty,
                    (int) $produk['stok']
                );
                continue;
            }

            $subtotal    = (float) $produk['harga'] * $qty;
            $totalHarga += $subtotal;

            $lineItems[] = [
                'id_produk' => $produk['id_produk'],
                'harga'     => $produk['harga'],
                'qty'       => $qty,
                'subtotal'  => $subtotal,
                'stokAwal'  => (int) $produk['stok'],
            ];
        }

        if (! empty($shortages)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Stok tidak cukup untuk: ' . implode('; ', $shortages));
        }

        if (empty($lineItems)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Keranjang kosong. Tambahkan minimal satu produk.');
        }

        $bayar = (float) $this->request->getPost('bayar');

        if ($bayar < $totalHarga) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jumlah bayar kurang dari total harga.');
        }

        // --- Step 2: stock confirmed sufficient, commit atomically ---
        $db = \Config\Database::connect();
        $db->transStart();

        $kodePenjualan = $this->model->generateKodePenjualan();
        $noInvoice     = $this->model->generateNoInvoice();
        $kembalian     = $bayar - $totalHarga;

        $this->model->insert([
            'kode_penjualan'    => $kodePenjualan,
            'no_invoice'        => $noInvoice,
            'nama_customer'     => $this->request->getPost('nama_customer'),
            'telepon_customer'  => $this->request->getPost('telepon_customer'),
            'id_kasir'          => $this->currentUserId(),
            'total_harga'       => $totalHarga,
            'metode_bayar'      => $this->request->getPost('metode_bayar'),
            'bayar'             => $bayar,
            'kembalian'         => $kembalian,
            'status'            => 'completed',
            'tanggal_penjualan' => date('Y-m-d H:i:s'),
        ]);

        $idPenjualan = $this->model->getInsertID();

        $detailRows = [];

        foreach ($lineItems as $item) {
            $detailRows[] = [
                'id_penjualan' => $idPenjualan,
                'id_produk'    => $item['id_produk'],
                'harga'        => $item['harga'],
                'qty'          => $item['qty'],
                'subtotal'     => $item['subtotal'],
            ];

            // Deduct stock
            $this->produkModel->update($item['id_produk'], [
                'stok' => $item['stokAwal'] - $item['qty'],
            ]);
        }

        $this->detailModel->insertBatchRows($detailRows);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan penjualan karena kesalahan sistem. Stok tidak berubah.');
        }

        $this->logActivity(
            "Penjualan baru: {$kodePenjualan} ({$noInvoice}) - total Rp " . number_format($totalHarga, 0, ',', '.'),
            'Penjualan'
        );

        return redirect()->to('/penjualan/detail/' . $idPenjualan)
            ->with('success', 'Penjualan berhasil disimpan.');
    }

    // ------------------------------------------------------------------
    // DETAIL — show transaction + line items (for receipt-style view)
    // ------------------------------------------------------------------
    public function detail(int $id): string
    {
        $penjualan = $this->model->findWithRelations($id);

        if ($penjualan === null) {
            return redirect()->to('/penjualan')
                ->with('error', 'Penjualan tidak ditemukan.');
        }

        return view('penjualan/penjualan_detail', [
            'pageTitle'      => 'Detail Penjualan',
            'penjualan'      => $penjualan,
            'detailPenjualan'=> $this->detailModel->getByPenjualan($id),
        ]);
    }
}