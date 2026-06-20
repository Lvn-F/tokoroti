<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Produk extends Model
{
    protected $table            = 'produk';
    protected $primaryKey       = 'id_produk';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'kode_produk',
        'id_kategori',
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
        'gambar',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    protected $validationRules = [
        'id_kategori' => 'required|integer',
        'nama_produk' => 'required|max_length[100]',
        'deskripsi'   => 'permit_empty',
        'harga'       => 'required|decimal',
        'stok'        => 'permit_empty|integer',
        'status'      => 'permit_empty|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'id_kategori' => [
            'required' => 'Kategori wajib dipilih.',
            'integer'  => 'Kategori tidak valid.',
        ],
        'nama_produk' => [
            'required'   => 'Nama produk wajib diisi.',
            'max_length' => 'Nama produk maksimal 100 karakter.',
        ],
        'harga' => [
            'required' => 'Harga wajib diisi.',
            'decimal'  => 'Harga harus berupa angka.',
        ],
    ];

    /**
     * Directory (relative to FCPATH/public) where product images are stored.
     */
    public const UPLOAD_PATH = 'uploads/produk/';

    /**
     * Search + paginate for the index page. Joins kategori_produk so the
     * view can show the category name without an extra query per row.
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(string $keyword = '', int $perPage = 10): array
    {
        $builder = $this->select('produk.*, kategori_produk.nama_kategori')
            ->join('kategori_produk', 'kategori_produk.id_kategori = produk.id_kategori', 'left');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('produk.nama_produk', $keyword)
                ->orLike('produk.kode_produk', $keyword)
                ->groupEnd();
        }

        $builder->orderBy('produk.nama_produk', 'ASC');

        return $this->paginate($perPage);
    }

    /**
     * Find a single produk row joined with its category name.
     * Useful for detail/edit views that want nama_kategori without
     * a second lookup.
     *
     * @return array<string, mixed>|null
     */
    public function findWithKategori(int $id): ?array
    {
        return $this->select('produk.*, kategori_produk.nama_kategori')
            ->join('kategori_produk', 'kategori_produk.id_kategori = produk.id_kategori', 'left')
            ->where('produk.id_produk', $id)
            ->first();
    }

    /**
     * Generate the next sequential kode_produk, e.g. PRD-0001, PRD-0002, ...
     */
    public function generateKodeProduk(): string
    {
        $last = $this->select('kode_produk')
            ->like('kode_produk', 'PRD-', 'after')
            ->orderBy('id_produk', 'DESC')
            ->first();

        $nextNumber = 1;

        if ($last !== null && preg_match('/PRD-(\d+)/', $last['kode_produk'], $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'PRD-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Public URL for a product's image, or null if it has none.
     * Does not check file existence on disk — that's the controller's job
     * if it needs to guard against missing files.
     */
    public function imageUrl(?string $filename): ?string
    {
        if (empty($filename)) {
            return null;
        }

        return base_url(self::UPLOAD_PATH . $filename);
    }
    /**
     * Count active products and total stock value (for dashboard cards).
     */
    public function countActive(): int
    {
        return $this->where('status', 'active')->countAllResults();
    }
}