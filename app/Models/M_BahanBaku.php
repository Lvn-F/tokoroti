<?php

namespace App\Models;

use CodeIgniter\Model;

class M_BahanBaku extends Model
{
    protected $table            = 'bahan_baku';
    protected $primaryKey       = 'id_bahan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'kode_bahan',
        'nama_bahan',
        'satuan',
        'stok',
        'stok_minimum',
        'harga_beli',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    /**
     * Allowed units. Single source of truth for both validation
     * and the <select> dropdown in views.
     *
     * @var list<string>
     */
    public const SATUAN = ['pcs', 'kg', 'gram', 'liter', 'ml', 'butir','sdt'];

    protected $validationRules = [
        'nama_bahan'   => 'required|max_length[100]',
        'satuan'       => 'required|in_list[pcs,kg,gram,liter,ml,butir,sdt]',
        'stok'         => 'permit_empty|decimal',
        'stok_minimum' => 'permit_empty|decimal',
        'harga_beli'   => 'required|decimal',
    ];

    protected $validationMessages = [
        'nama_bahan' => [
            'required'   => 'Nama bahan wajib diisi.',
            'max_length' => 'Nama bahan maksimal 100 karakter.',
        ],
        'satuan' => [
            'required' => 'Satuan wajib dipilih.',
            'in_list'  => 'Satuan tidak valid.',
        ],
        'harga_beli' => [
            'required' => 'Harga beli wajib diisi.',
            'decimal'  => 'Harga beli harus berupa angka.',
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
                ->like('nama_bahan', $keyword)
                ->orLike('kode_bahan', $keyword)
                ->groupEnd();
        }

        $this->orderBy('nama_bahan', 'ASC');

        return $this->paginate($perPage);
    }

    /**
     * Generate the next sequential kode_bahan, e.g. BHN-0001, BHN-0002, ...
     * Looks at the highest existing numeric suffix so it keeps working
     * correctly even if rows are deleted out of order.
     */
    public function generateKodeBahan(): string
    {
        $last = $this->select('kode_bahan')
            ->like('kode_bahan', 'BHN-', 'after')
            ->orderBy('id_bahan', 'DESC')
            ->first();

        $nextNumber = 1;

        if ($last !== null && preg_match('/BHN-(\d+)/', $last['kode_bahan'], $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'BHN-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Whether a given stock row is at or below its minimum threshold.
     *
     * @param array<string, mixed> $bahan A bahan_baku row (must contain 'stok', 'stok_minimum').
     */
    public function isLowStock(array $bahan): bool
    {
        return (float) $bahan['stok'] <= (float) $bahan['stok_minimum'];
    }
    /**
     * Count bahan baku currently at/below their minimum stock threshold.
     */
    public function countLowStock(): int
    {
        return $this->where('stok <=', 'stok_minimum', false)->countAllResults();
    }
}