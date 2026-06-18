<?php

namespace App\Models;

use CodeIgniter\Model;

class M_KategoriProduk extends Model
{
    protected $table            = 'kategori_produk';
    protected $primaryKey       = 'id_kategori';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'nama_kategori',
        'deskripsi',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';   // table has no updated_at column
    protected $deletedField  = '';

    protected $validationRules = [
        'nama_kategori' => 'required|max_length[100]',
        'deskripsi'     => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'nama_kategori' => [
            'required'   => 'Nama kategori wajib diisi.',
            'max_length' => 'Nama kategori maksimal 100 karakter.',
        ],
    ];

    /**
     * Search + paginate for the index page.
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(string $keyword = '', int $perPage = 10): array
    {
        if ($keyword !== '') {
            $this->groupStart()
                ->like('nama_kategori', $keyword)
                ->orLike('deskripsi', $keyword)
                ->groupEnd();
        }

        $this->orderBy('nama_kategori', 'ASC');

        return $this->paginate($perPage);
    }
}