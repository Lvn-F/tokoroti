<?php

namespace App\Controllers;

use App\Models\M_KategoriProduk;
use App\Models\M_Produk;
use CodeIgniter\HTTP\Files\UploadedFile;

class C_Produk extends BaseController
{
    protected M_Produk $model;
    protected M_KategoriProduk $kategoriModel;

    /**
     * Folder on disk (relative to FCPATH) where product images live.
     * Matches M_Produk::UPLOAD_PATH but as a filesystem path, not a URL.
     */
    protected string $uploadPath;

    public function __construct()
    {
        $this->model         = new M_Produk();
        $this->kategoriModel = new M_KategoriProduk();
        $this->uploadPath    = FCPATH . M_Produk::UPLOAD_PATH;
    }

    // ------------------------------------------------------------------
    // INDEX — list with search + pagination
    // ------------------------------------------------------------------
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

    // ------------------------------------------------------------------
    // CREATE — show form
    // ------------------------------------------------------------------
    public function create(): string
    {
        return view('produk/produk_create', [
            'pageTitle'   => 'Tambah Produk',
            'kategoris'   => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'kodePreview' => $this->model->generateKodeProduk(),
        ]);
    }

    // ------------------------------------------------------------------
    // STORE — handle form submission (with image upload)
    // ------------------------------------------------------------------
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

    // ------------------------------------------------------------------
    // EDIT — show form pre-filled
    // ------------------------------------------------------------------
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
            'model'     => $this->model, // for imageUrl() in the view
        ]);
    }

    // ------------------------------------------------------------------
    // UPDATE — handle edit form submission (image optional on edit)
    // ------------------------------------------------------------------
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

        // Image is optional on edit — only validate it if a new file was chosen.
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

        // Only touch the image field if a new valid file was actually uploaded.
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

    // ------------------------------------------------------------------
    // DELETE — confirm then remove (and clean up the image file)
    // ------------------------------------------------------------------
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

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Move an uploaded file into the produk upload folder with a
     * randomized, collision-safe name. Returns the stored filename
     * on success, or null on failure.
     */
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

    /**
     * Delete a product image file from disk, if it exists.
     * Safe to call with null/empty filenames — does nothing.
     */
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