<?php

namespace App\Controllers;

use App\Models\M_KategoriProduk;
use App\Models\M_Produk;
use CodeIgniter\HTTP\Files\UploadedFile;

class C_Produk extends BaseController
{
    protected M_Produk $model;
    protected M_KategoriProduk $kategoriModel;

    protected string $uploadPath;

    public function __construct()
    {
        $this->model         = new M_Produk();
        $this->kategoriModel = new M_KategoriProduk();
        $this->uploadPath    = FCPATH . M_Produk::UPLOAD_PATH;
    }

    public function index(): string
    {
        $keyword = $this->request->getGet('q') ?? '';
        $perPage = 10;

        $data = [
            'pageTitle' => 'Produk',
            'keyword'   => $keyword,
            'produks'   => $this->model->search($keyword, $perPage),
            'pager'     => $this->model->pager,
            'model'     => $this->model, // for imageUrl() in the view
        ];

        return view('produk/produk_index', $data);
    }

    public function create(): string
    {
        return view('produk/produk_create', [
            'pageTitle'   => 'Tambah Produk',
            'kategoris'   => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'kodePreview' => $this->model->generateKodeProduk(),
        ]);
    }

    public function store()
    {
        $rules = [
            'id_kategori' => 'required|integer',
            'nama_produk' => 'required|max_length[100]',
            'deskripsi'   => 'permit_empty',
            'harga'       => 'required|decimal',
            'stok'        => 'permit_empty|integer',
            'gambar'      => 'uploaded[gambar]'
                . '|max_size[gambar,2048]'
                . '|is_image[gambar]'
                . '|ext_in[gambar,jpg,jpeg,png,webp]',
        ];

        $messages = [
            'gambar' => [
                'uploaded'  => 'Gambar produk wajib diunggah.',
                'max_size'  => 'Ukuran gambar maksimal 2MB.',
                'is_image'  => 'File yang diunggah harus berupa gambar.',
                'ext_in'    => 'Format gambar harus jpg, jpeg, png, atau webp.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $filename = $this->handleUpload($this->request->getFile('gambar'));

        if ($filename === null) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengunggah gambar. Silakan coba lagi.');
        }

        $kodeProduk = $this->model->generateKodeProduk();

        $this->model->insert([
            'kode_produk' => $kodeProduk,
            'id_kategori' => $this->request->getPost('id_kategori'),
            'nama_produk' => $this->request->getPost('nama_produk'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'harga'       => $this->request->getPost('harga'),
            'stok'        => $this->request->getPost('stok') ?: 0,
            'gambar'      => $filename,
            'status'      => 'active',
        ]);

        $this->logActivity(
            "Menambah produk: {$kodeProduk} - " . $this->request->getPost('nama_produk'),
            'Produk'
        );

        return redirect()->to('/produk')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        $produk = $this->model->find($id);

        if ($produk === null) {
            return redirect()->to('/produk')
                ->with('error', 'Produk tidak ditemukan.');
        }

        return view('produk/produk_edit', [
            'pageTitle' => 'Edit Produk',
            'produk'    => $produk,
            'kategoris' => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'model'     => $this->model,
        ]);
    }

    public function update(int $id)
    {
        $produk = $this->model->find($id);

        if ($produk === null) {
            return redirect()->to('/produk')
                ->with('error', 'Produk tidak ditemukan.');
        }

        $rules = [
            'id_kategori' => 'required|integer',
            'nama_produk' => 'required|max_length[100]',
            'deskripsi'   => 'permit_empty',
            'harga'       => 'required|decimal',
            'stok'        => 'permit_empty|integer',
            'status'      => 'permit_empty|in_list[active,inactive]',
        ];

        $messages = [];

        $newImage = $this->request->getFile('gambar');

        if ($newImage !== null && $newImage->isValid() && ! $newImage->hasMoved()) {
            $rules['gambar'] = 'max_size[gambar,2048]|is_image[gambar]|ext_in[gambar,jpg,jpeg,png,webp]';
            $messages['gambar'] = [
                'max_size' => 'Ukuran gambar maksimal 2MB.',
                'is_image' => 'File yang diunggah harus berupa gambar.',
                'ext_in'   => 'Format gambar harus jpg, jpeg, png, atau webp.',
            ];
        }

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'id_kategori' => $this->request->getPost('id_kategori'),
            'nama_produk' => $this->request->getPost('nama_produk'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'harga'       => $this->request->getPost('harga'),
            'stok'        => $this->request->getPost('stok') ?: 0,
            'status'      => $this->request->getPost('status') ?: 'active',
        ];

        if ($newImage !== null && $newImage->isValid() && ! $newImage->hasMoved()) {
            $filename = $this->handleUpload($newImage);

            if ($filename === null) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal mengunggah gambar baru. Silakan coba lagi.');
            }

            $this->deleteImageFile($produk['gambar']);
            $updateData['gambar'] = $filename;
        }

        $this->model->update($id, $updateData);

        $this->logActivity(
            "Mengedit produk: {$produk['kode_produk']} - " . $this->request->getPost('nama_produk'),
            'Produk'
        );

        return redirect()->to('/produk')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $produk = $this->model->find($id);

        if ($produk === null) {
            return redirect()->to('/produk')
                ->with('error', 'Produk tidak ditemukan.');
        }

        $this->model->delete($id);
        $this->deleteImageFile($produk['gambar']);

        $this->logActivity(
            "Menghapus produk: {$produk['kode_produk']} - {$produk['nama_produk']}",
            'Produk'
        );

        return redirect()->to('/produk')
            ->with('success', 'Produk berhasil dihapus.');
    }

    protected function handleUpload(?UploadedFile $file): ?string
    {
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        if (! is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }

        $newName = $file->getRandomName();

        try {
            $file->move($this->uploadPath, $newName);
        } catch (\Throwable $e) {
            log_message('error', 'Produk image upload failed: ' . $e->getMessage());

            return null;
        }

        return $newName;
    }

    protected function deleteImageFile(?string $filename): void
    {
        if (empty($filename)) {
            return;
        }

        $path = $this->uploadPath . $filename;

        if (is_file($path)) {
            @unlink($path);
        }
    }
}