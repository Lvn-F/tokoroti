<?php

namespace App\Controllers;

use App\Models\M_BahanBaku;
use App\Models\M_DetailProduksi;
use App\Models\M_Produk;
use App\Models\M_Produksi;
use App\Models\M_Resep;

class C_Produksi extends BaseController
{
    protected M_Produksi $model;
    protected M_Produk $produkModel;
    protected M_Resep $resepModel;
    protected M_BahanBaku $bahanModel;
    protected M_DetailProduksi $detailModel;

    public function __construct()
    {
        $this->model       = new M_Produksi();
        $this->produkModel = new M_Produk();
        $this->resepModel  = new M_Resep();
        $this->bahanModel  = new M_BahanBaku();
        $this->detailModel = new M_DetailProduksi();
    }

    public function index(): string
    {
        $keyword = $this->request->getGet('q') ?? '';
        $status  = $this->request->getGet('status') ?? '';
        $perPage = 10;

        $data = [
            'pageTitle'  => 'Produksi',
            'keyword'    => $keyword,
            'status'     => $status,
            'statusList' => M_Produksi::STATUS,
            'produksis'  => $this->model->search($keyword, $status, $perPage),
            'pager'      => $this->model->pager,
            'model'      => $this->model,
        ];

        return view('produksi/produksi_index', $data);
    }

    public function create(): string
    {
        return view('produksi/produksi_create', [
            'pageTitle'   => 'Tambah Produksi',
            'produks'     => $this->produkModel->where('status', 'active')->orderBy('nama_produk', 'ASC')->findAll(),
            'kodePreview' => $this->model->generateKodeProduksi(),
        ]);
    }

    public function store()
    {
        $rules = [
            'id_produk'        => 'required|integer',
            'jumlah_produksi'  => 'required|integer|greater_than[0]',
            'tanggal_produksi' => 'required|valid_date',
            'catatan'          => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $produk = $this->produkModel->find($this->request->getPost('id_produk'));

        if ($produk === null) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Produk tidak ditemukan.');
        }

        $kodeProduksi = $this->model->generateKodeProduksi();

        $this->model->insert([
            'kode_produksi'    => $kodeProduksi,
            'id_produk'        => $this->request->getPost('id_produk'),
            'id_user'          => $this->currentUserId(),
            'jumlah_produksi'  => $this->request->getPost('jumlah_produksi'),
            'tanggal_produksi' => $this->request->getPost('tanggal_produksi'),
            'status'           => 'proses',
            'catatan'          => $this->request->getPost('catatan'),
        ]);

        $this->logActivity(
            "Membuat produksi: {$kodeProduksi} - {$produk['nama_produk']} ({$this->request->getPost('jumlah_produksi')} unit)",
            'Produksi'
        );

        return redirect()->to('/produksi')
            ->with('success', 'Produksi berhasil dibuat dengan status Proses.');
    }

    public function detail(int $id): string
    {
        $produksi = $this->model->findWithRelations($id);

        if ($produksi === null) {
            return redirect()->to('/produksi')
                ->with('error', 'Produksi tidak ditemukan.');
        }

        return view('produksi/produksi_detail', [
            'pageTitle'        => 'Detail Produksi',
            'produksi'         => $produksi,
            'detailProduksi'   => $this->detailModel->getByProduksi($id),
            'resep'            => $this->resepModel->getByProduk($produksi['id_produk']),
            'model'            => $this->model, // for isEditable() in the view
            'statusList'       => M_Produksi::STATUS,
        ]);
    }

    public function updateStatus(int $id)
    {
        $produksi = $this->model->find($id);

        if ($produksi === null) {
            return redirect()->to('/produksi')
                ->with('error', 'Produksi tidak ditemukan.');
        }

        if (! $this->model->isEditable($produksi)) {
            return redirect()->to('/produksi/detail/' . $id)
                ->with('error', 'Status produksi ini sudah final dan tidak dapat diubah lagi.');
        }

        $newStatus = $this->request->getPost('status');

        if (! in_array($newStatus, M_Produksi::STATUS, true) || $newStatus === 'proses') {
            return redirect()->to('/produksi/detail/' . $id)
                ->with('error', 'Status tujuan tidak valid.');
        }

        if ($newStatus === 'gagal') {
            $this->model->update($id, ['status' => 'gagal']);

            $this->logActivity(
                "Mengubah status produksi {$produksi['kode_produksi']} menjadi Gagal",
                'Produksi'
            );

            return redirect()->to('/produksi/detail/' . $id)
                ->with('success', 'Status produksi diubah menjadi Gagal.');
        }

        return $this->completeProduksi($produksi);
    }

    protected function completeProduksi(array $produksi)
    {
        $idProduksi = (int) $produksi['id_produksi'];
        $jumlah     = (int) $produksi['jumlah_produksi'];

        $resep = $this->resepModel->getByProduk((int) $produksi['id_produk']);

        if (empty($resep)) {
            return redirect()->to('/produksi/detail/' . $idProduksi)
                ->with('error', 'Produk ini belum memiliki resep. Tambahkan resep terlebih dahulu sebelum menyelesaikan produksi.');
        }

        $shortages = [];

        foreach ($resep as $item) {
            $required = (float) $item['jumlah_dibutuhkan'] * $jumlah;
            $available = (float) $item['stok'];

            if ($available < $required) {
                $shortages[] = sprintf(
                    '%s (butuh %s %s, tersedia %s %s)',
                    $item['nama_bahan'],
                    number_format($required, 2),
                    $item['satuan'],
                    number_format($available, 2),
                    $item['satuan']
                );
            }
        }

        if (! empty($shortages)) {
            return redirect()->to('/produksi/detail/' . $idProduksi)
                ->with('error', 'Stok bahan baku tidak cukup: ' . implode('; ', $shortages));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $detailRows = [];

        foreach ($resep as $item) {
            $jumlahTerpakai = (float) $item['jumlah_dibutuhkan'] * $jumlah;

            $this->bahanModel->update($item['id_bahan'], [
                'stok' => (float) $item['stok'] - $jumlahTerpakai,
            ]);

            $detailRows[] = [
                'id_produksi'     => $idProduksi,
                'id_bahan'        => $item['id_bahan'],
                'jumlah_terpakai' => $jumlahTerpakai,
            ];
        }

        $this->detailModel->insertBatchRows($detailRows);

        $produk = $this->produkModel->find($produksi['id_produk']);
        $this->produkModel->update($produksi['id_produk'], [
            'stok' => (int) $produk['stok'] + $jumlah,
        ]);

        $this->model->update($idProduksi, ['status' => 'selesai']);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/produksi/detail/' . $idProduksi)
                ->with('error', 'Gagal menyelesaikan produksi karena kesalahan sistem. Stok tidak berubah.');
        }

        $this->logActivity(
            "Menyelesaikan produksi: {$produksi['kode_produksi']} - {$jumlah} unit (stok bahan baku & produk diperbarui)",
            'Produksi'
        );

        return redirect()->to('/produksi/detail/' . $idProduksi)
            ->with('success', 'Produksi berhasil diselesaikan. Stok bahan baku & produk telah diperbarui.');
    }
}