<?php

namespace App\Controllers;

use App\Models\M_KategoriProduk;

class C_KategoriProduk extends BaseController
{
    protected M_KategoriProduk $model;

    public function __construct()
    {
        $this->model = new M_KategoriProduk();
    }

    // ------------------------------------------------------------------
    // INDEX — list with search + pagination
    // ------------------------------------------------------------------
    public function index(): string
    {
        $keyword = $this->request->getGet('q') ?? '';
        $perPage = 10;

        $data = [
            'pageTitle'  => 'Kategori Produk',
            'keyword'    => $keyword,
            'kategoris'  => $this->model->search($keyword, $perPage),
            'pager'      => $this->model->pager,
        ];

        return view('kategori/kategori_index', $data);
    }

    // ------------------------------------------------------------------
    // CREATE — show form
    // ------------------------------------------------------------------
    public function create(): string
    {
        return view('kategori/kategori_create', [
            'pageTitle' => 'Tambah Kategori',
        ]);
    }

    // ------------------------------------------------------------------
    // STORE — handle form submission
    // ------------------------------------------------------------------
    public function store()
    {
        $rules = [
            'nama_kategori' => 'required|max_length[100]',
            'deskripsi'     => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->model->insert([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
        ]);

        $this->logActivity(
            'Menambah kategori: ' . $this->request->getPost('nama_kategori'),
            'Kategori Produk'
        );

        return redirect()->to('/kategori')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    // ------------------------------------------------------------------
    // EDIT — show form pre-filled
    // ------------------------------------------------------------------
    public function edit(int $id): string
    {
        $kategori = $this->model->find($id);

        if ($kategori === null) {
            return redirect()->to('/kategori')
                ->with('error', 'Kategori tidak ditemukan.');
        }

        return view('kategori/kategori_edit', [
            'pageTitle' => 'Edit Kategori',
            'kategori'  => $kategori,
        ]);
    }

    // ------------------------------------------------------------------
    // UPDATE — handle edit form submission
    // ------------------------------------------------------------------
    public function update(int $id)
    {
        $kategori = $this->model->find($id);

        if ($kategori === null) {
            return redirect()->to('/kategori')
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $rules = [
            'nama_kategori' => 'required|max_length[100]',
            'deskripsi'     => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
        ]);

        $this->logActivity(
            'Mengedit kategori: ' . $this->request->getPost('nama_kategori'),
            'Kategori Produk'
        );

        return redirect()->to('/kategori')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    // ------------------------------------------------------------------
    // DELETE — confirm then remove
    // ------------------------------------------------------------------
    public function delete(int $id)
    {
        $kategori = $this->model->find($id);

        if ($kategori === null) {
            return redirect()->to('/kategori')
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $this->model->delete($id);

        $this->logActivity(
            'Menghapus kategori: ' . $kategori['nama_kategori'],
            'Kategori Produk'
        );

        return redirect()->to('/kategori')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}