<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Produksi extends Model
{
    protected $table            = 'produksi';
    protected $primaryKey       = 'id_produksi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'kode_produksi',
        'id_produk',
        'id_user',
        'jumlah_produksi',
        'tanggal_produksi',
        'status',
        'catatan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    public const STATUS = ['proses', 'selesai', 'gagal'];

    protected $validationRules = [
        'id_produk'        => 'required|integer',
        'jumlah_produksi'  => 'required|integer|greater_than[0]',
        'tanggal_produksi' => 'required|valid_date',
        'catatan'          => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'id_produk' => [
            'required' => 'Produk wajib dipilih.',
        ],
        'jumlah_produksi' => [
            'required'     => 'Jumlah produksi wajib diisi.',
            'greater_than' => 'Jumlah produksi harus lebih dari 0.',
        ],
        'tanggal_produksi' => [
            'required'   => 'Tanggal produksi wajib diisi.',
            'valid_date' => 'Tanggal produksi tidak valid.',
        ],
    ];

    /**
     * @return array<int, array<string, mixed>>
     */
    public function search(string $keyword = '', string $status = '', int $perPage = 10): array
    {
        $builder = $this->select('produksi.*, produk.nama_produk, produk.kode_produk as kode_produk_ref, users.nama_user')
            ->join('produk', 'produk.id_produk = produksi.id_produk', 'left')
            ->join('users', 'users.id_user = produksi.id_user', 'left');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('produksi.kode_produksi', $keyword)
                ->orLike('produk.nama_produk', $keyword)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('produksi.status', $status);
        }

        $builder->orderBy('produksi.created_at', 'DESC');

        return $this->paginate($perPage);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findWithRelations(int $id): ?array
    {
        return $this->select('produksi.*, produk.nama_produk, produk.kode_produk as kode_produk_ref, users.nama_user')
            ->join('produk', 'produk.id_produk = produksi.id_produk', 'left')
            ->join('users', 'users.id_user = produksi.id_user', 'left')
            ->where('produksi.id_produksi', $id)
            ->first();
    }

    public function generateKodeProduksi(): string
    {
        $last = $this->select('kode_produksi')
            ->like('kode_produksi', 'PRO-', 'after')
            ->orderBy('id_produksi', 'DESC')
            ->first();

        $nextNumber = 1;

        if ($last !== null && preg_match('/PRO-(\d+)/', $last['kode_produksi'], $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'PRO-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @param array<string, mixed> $produksi
     */
    public function isEditable(array $produksi): bool
    {
        return $produksi['status'] === 'proses';
    }
    /**
     * @return array<string, int>
     */
    public function countByStatus(): array
    {
        $rows = $this->select('status, COUNT(*) as total')
            ->groupBy('status')
            ->findAll();

        $result = array_fill_keys(self::STATUS, 0);

        foreach ($rows as $r) {
            $result[$r['status']] = (int) $r['total'];
        }

        return $result;
    }
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getByDateRange(string $startDate, string $endDate, string $status = ''): array
    {
        $builder = $this->select('produksi.*, produk.nama_produk, produk.kode_produk as kode_produk_ref, users.nama_user')
            ->join('produk', 'produk.id_produk = produksi.id_produk', 'left')
            ->join('users', 'users.id_user = produksi.id_user', 'left')
            ->where('produksi.tanggal_produksi >=', $startDate)
            ->where('produksi.tanggal_produksi <=', $endDate);

        if ($status !== '') {
            $builder->where('produksi.status', $status);
        }

        return $builder->orderBy('produksi.tanggal_produksi', 'ASC')->findAll();
    }
}