<?php

namespace App\Controllers;

use App\Models\M_BahanBaku;
use App\Models\M_Produk;
use App\Models\M_Resep;

class C_Resep extends BaseController
{
    protected M_Resep $model;
    protected M_Produk $produkModel;
    protected M_BahanBaku $bahanModel;

    public function __construct()
    {
        $this->model       = new M_Resep();
        $this->produkModel = new M_Produk();
        $this->bahanModel  = new M_BahanBaku();
    }

    public function index(int $idProduk): string
    {
        $produk = $this->produkModel->find($idProduk);

        if ($produk === null) {
            return redirect()->to('/produk')
                ->with('error', 'Produk tidak ditemukan.');
        }

        $resep = $this->model->getByProduk($idProduk);

        $usedBahanIds = array_column($resep, 'id_bahan');

        $availableBahan = $this->bahanModel
            ->orderBy('nama_bahan', 'ASC')
            ->findAll();

        if (! empty($usedBahanIds)) {
            $availableBahan = array_values(array_filter(
                $availableBahan,
                static fn ($b) => ! in_array((int) $b['id_bahan'], $usedBahanIds, true)
            ));
        }

        $totalBiaya = $this->model->getTotalBiayaByProduk($idProduk);
        $hargaJual  = (float) $produk['harga'];
        $margin     = $hargaJual - $totalBiaya;
        $marginPct  = $hargaJual > 0 ? ($margin / $hargaJual) * 100 : 0;

        return view('resep/resep_index', [
            'pageTitle'      => 'Resep - ' . $produk['nama_produk'],
            'produk'         => $produk,
            'resep'          => $resep,
            'availableBahan' => $availableBahan,
            'totalBiaya'     => $totalBiaya,
            'margin'         => $margin,
            'marginPct'      => $marginPct,
        ]);
    }

    public function store(int $idProduk)
    {
        $produk = $this->produkModel->find($idProduk);

        if ($produk === null) {
            return redirect()->to('/produk')
                ->with('error', 'Produk tidak ditemukan.');
        }

        $rules = [
            'id_bahan'          => 'required|integer',
            'jumlah_dibutuhkan' => 'required|decimal|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/produk/resep/' . $idProduk)
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $idBahan = (int) $this->request->getPost('id_bahan');

        $exists = $this->model->findByKeys($idProduk, $idBahan);

        if ($exists !== null) {
            return redirect()->to('/produk/resep/' . $idProduk)
                ->with('error', 'Bahan ini sudah ada dalam resep. Hapus dulu jika ingin mengubah jumlahnya.');
        }

        $bahan = $this->bahanModel->find($idBahan);

        $this->model->insert([
            'id_produk'         => $idProduk,
            'id_bahan'          => $idBahan,
            'jumlah_dibutuhkan' => $this->request->getPost('jumlah_dibutuhkan'),
        ]);

        $this->logActivity(
            "Menambah resep: {$produk['nama_produk']} + {$bahan['nama_bahan']} ({$this->request->getPost('jumlah_dibutuhkan')} {$bahan['satuan']})",
            'Resep'
        );

        return redirect()->to('/produk/resep/' . $idProduk)
            ->with('success', 'Bahan berhasil ditambahkan ke resep.');
    }

    public function delete(int $idProduk, int $idBahan)
    {
        $produk = $this->produkModel->find($idProduk);

        if ($produk === null) {
            return redirect()->to('/produk')
                ->with('error', 'Produk tidak ditemukan.');
        }

        $resepRow = $this->model->findByKeys($idProduk, $idBahan);

        if ($resepRow === null) {
            return redirect()->to('/produk/resep/' . $idProduk)
                ->with('error', 'Item resep tidak ditemukan.');
        }

        $bahan = $this->bahanModel->find($idBahan);

        $this->model->deleteByKeys($idProduk, $idBahan);

        $this->logActivity(
            "Menghapus resep: {$produk['nama_produk']} - " . ($bahan['nama_bahan'] ?? 'bahan tidak diketahui'),
            'Resep'
        );

        return redirect()->to('/produk/resep/' . $idProduk)
            ->with('success', 'Bahan berhasil dihapus dari resep.');
    }
}