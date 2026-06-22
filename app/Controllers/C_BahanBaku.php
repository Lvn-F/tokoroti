<?php

namespace App\Controllers;

use App\Models\M_BahanBaku;

class C_BahanBaku extends BaseController
{
    protected M_BahanBaku $model;

    public function __construct()
    {
        $this->model = new M_BahanBaku();
    }

    public function index(): string
    {
        $keyword = $this->request->getGet('q') ?? '';
        $perPage = 10;

        $data = [
            'pageTitle' => 'Bahan Baku',
            'keyword'   => $keyword,
            'bahans'    => $this->model->search($keyword, $perPage),
            'pager'     => $this->model->pager,
            'model'     => $this->model, // for isLowStock() in the view
        ];

        return view('bahanbaku/bahanbaku_index', $data);
    }

    public function create(): string
    {
        return view('bahanbaku/bahanbaku_create', [
            'pageTitle'    => 'Tambah Bahan Baku',
            'satuanList'   => M_BahanBaku::SATUAN,
            'kodePreview'  => $this->model->generateKodeBahan(),
        ]);
    }

    public function store()
    {
        $rules = [
            'nama_bahan'   => 'required|max_length[100]',
            'satuan'       => 'required|in_list[pcs,kg,gram,liter,ml,butir,sdt]',
            'stok'         => 'permit_empty|decimal',
            'stok_minimum' => 'permit_empty|decimal',
            'harga_beli'   => 'required|decimal',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $kodeBahan = $this->model->generateKodeBahan();

        $this->model->insert([
            'kode_bahan'   => $kodeBahan,
            'nama_bahan'   => $this->request->getPost('nama_bahan'),
            'satuan'       => $this->request->getPost('satuan'),
            'stok'         => $this->request->getPost('stok') ?: 0,
            'stok_minimum' => $this->request->getPost('stok_minimum') ?: 0,
            'harga_beli'   => $this->request->getPost('harga_beli'),
        ]);

        $this->logActivity(
            "Menambah bahan baku: {$kodeBahan} - " . $this->request->getPost('nama_bahan'),
            'Bahan Baku'
        );

        return redirect()->to('/bahan-baku')
            ->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        $bahan = $this->model->find($id);

        if ($bahan === null) {
            return redirect()->to('/bahan-baku')
                ->with('error', 'Bahan baku tidak ditemukan.');
        }

        return view('bahanbaku/bahanbaku_edit', [
            'pageTitle'  => 'Edit Bahan Baku',
            'bahan'      => $bahan,
            'satuanList' => M_BahanBaku::SATUAN,
        ]);
    }

    // ------------------------------------------------------------------
    // UPDATE — handle edit form submission
    // ------------------------------------------------------------------
    public function update(int $id)
    {
        $bahan = $this->model->find($id);

        if ($bahan === null) {
            return redirect()->to('/bahan-baku')
                ->with('error', 'Bahan baku tidak ditemukan.');
        }

        $rules = [
            'nama_bahan'   => 'required|max_length[100]',
            'satuan'       => 'required|in_list[pcs,kg,gram,liter,ml,butir,sdt]',
            'stok'         => 'permit_empty|decimal',
            'stok_minimum' => 'permit_empty|decimal',
            'harga_beli'   => 'required|decimal',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'nama_bahan'   => $this->request->getPost('nama_bahan'),
            'satuan'       => $this->request->getPost('satuan'),
            'stok'         => $this->request->getPost('stok') ?: 0,
            'stok_minimum' => $this->request->getPost('stok_minimum') ?: 0,
            'harga_beli'   => $this->request->getPost('harga_beli'),
        ]);

        $this->logActivity(
            "Mengedit bahan baku: {$bahan['kode_bahan']} - " . $this->request->getPost('nama_bahan'),
            'Bahan Baku'
        );

        return redirect()->to('/bahan-baku')
            ->with('success', 'Bahan baku berhasil diperbarui.');
    }

    // ------------------------------------------------------------------
    // DELETE — confirm then remove
    // ------------------------------------------------------------------
    public function delete(int $id)
    {
        $bahan = $this->model->find($id);

        if ($bahan === null) {
            return redirect()->to('/bahan-baku')
                ->with('error', 'Bahan baku tidak ditemukan.');
        }

        $this->model->delete($id);

        $this->logActivity(
            "Menghapus bahan baku: {$bahan['kode_bahan']} - {$bahan['nama_bahan']}",
            'Bahan Baku'
        );

        return redirect()->to('/bahan-baku')
            ->with('success', 'Bahan baku berhasil dihapus.');
    }
}