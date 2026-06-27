<?php

namespace App\Models;

use CodeIgniter\Model;

class M_BeliBahan extends Model
{
    protected $table            = 'beli_bahan';
    protected $primaryKey       = 'id_beli';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'kode_beli',
        'id_bahan',
        'satuan_beli',
        'jumlah_beli',
        'total_harga',
        'jumlah_konversi',
        'harga_per_satuan',
        'id_user',
        'tanggal_beli',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    /**
     * Units the user is allowed to pick when logging a purchase.
     *
     * @var list<string>
     */
    public const SATUAN_BELI = ['kg', 'gram', 'liter', 'ml', 'butir'];

    /**
     * Maps a purchase unit to its base (storage) unit + conversion factor.
     * factor = how many base units 1 purchase unit equals.
     *
     * @var array<string, array{base: string, factor: float}>
     */
    public const CONVERSION = [
        'kg'    => ['base' => 'gram', 'factor' => 1000],
        'gram'  => ['base' => 'gram', 'factor' => 1],
        'liter' => ['base' => 'ml',   'factor' => 1000],
        'ml'    => ['base' => 'ml',   'factor' => 1],
        'butir' => ['base' => 'butir', 'factor' => 1],
    ];

    protected $validationRules = [
        'id_bahan'     => 'required|integer',
        'satuan_beli'  => 'required|in_list[kg,gram,liter,ml,butir]',
        'jumlah_beli'  => 'required|decimal|greater_than[0]',
        'total_harga'  => 'required|decimal|greater_than[0]',
        'tanggal_beli' => 'required|valid_date',
    ];

    protected $validationMessages = [
        'id_bahan' => [
            'required' => 'Bahan baku wajib dipilih.',
        ],
        'jumlah_beli' => [
            'required'     => 'Jumlah beli wajib diisi.',
            'greater_than' => 'Jumlah beli harus lebih dari 0.',
        ],
        'total_harga' => [
            'required'     => 'Total harga wajib diisi.',
            'greater_than' => 'Total harga harus lebih dari 0.',
        ],
    ];

    /**
     * Returns the base unit a purchase unit belongs to.
     */
    public function baseUnitOf(string $satuanBeli): ?string
    {
        return self::CONVERSION[$satuanBeli]['base'] ?? null;
    }

    /**
     * Returns true if $satuanBeli can legally be used to buy a bahan
     * whose storage unit is $satuanDasar (e.g. 'liter' is NOT valid
     * for a bahan stored in 'gram').
     */
    public function isCompatible(string $satuanBeli, string $satuanDasar): bool
    {
        return $this->baseUnitOf($satuanBeli) === $satuanDasar;
    }

    /**
     * Converts a purchased quantity into the bahan's base/storage unit.
     */
    public function convertToBaseUnit(string $satuanBeli, float $jumlahBeli): float
    {
        $factor = self::CONVERSION[$satuanBeli]['factor'] ?? 1;

        return $jumlahBeli * $factor;
    }

    public function generateKodeBeli(): string
    {
        $last = $this->select('kode_beli')
            ->like('kode_beli', 'BLI-', 'after')
            ->orderBy('id_beli', 'DESC')
            ->first();

        $nextNumber = 1;

        if ($last !== null && preg_match('/BLI-(\d+)/', $last['kode_beli'], $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'BLI-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Records a purchase and, in the same DB transaction, increments
     * bahan_baku.stok and overwrites harga_beli with this purchase's
     * per-base-unit price (latest-price strategy, per project decision).
     *
     * @param array<string, mixed> $bahan A bahan_baku row (must contain id_bahan, satuan, stok).
     *
     * @return array{success: bool, error?: string, idBeli?: int, kodeBeli?: string}
     */
    public function recordPurchase(array $bahan, string $satuanBeli, float $jumlahBeli, float $totalHarga, string $tanggalBeli, int $idUser): array
    {
        $satuanDasar = $bahan['satuan'];

        if (! $this->isCompatible($satuanBeli, $satuanDasar)) {
            return [
                'success' => false,
                'error'   => "Satuan beli '{$satuanBeli}' tidak cocok dengan satuan dasar bahan ini ('{$satuanDasar}').",
            ];
        }

        $jumlahKonversi = $this->convertToBaseUnit($satuanBeli, $jumlahBeli);
        $hargaPerSatuan = $jumlahKonversi > 0 ? $totalHarga / $jumlahKonversi : 0;

        $db = \Config\Database::connect();
        $db->transStart();

        $kodeBeli = $this->generateKodeBeli();

        $this->insert([
            'kode_beli'        => $kodeBeli,
            'id_bahan'         => $bahan['id_bahan'],
            'satuan_beli'      => $satuanBeli,
            'jumlah_beli'      => $jumlahBeli,
            'total_harga'      => $totalHarga,
            'jumlah_konversi'  => $jumlahKonversi,
            'harga_per_satuan' => $hargaPerSatuan,
            'id_user'          => $idUser,
            'tanggal_beli'     => $tanggalBeli,
        ]);

        $idBeli = $this->getInsertID();

        $bahanModel = new M_BahanBaku();
        $bahanModel->update($bahan['id_bahan'], [
            'stok'       => (float) $bahan['stok'] + $jumlahKonversi,
            'harga_beli' => $hargaPerSatuan,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'error' => 'Gagal menyimpan pembelian karena kesalahan sistem.'];
        }

        return ['success' => true, 'idBeli' => $idBeli, 'kodeBeli' => $kodeBeli];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function search(string $keyword = '', int $perPage = 10): array
    {
        $builder = $this->select('beli_bahan.*, bahan_baku.nama_bahan, bahan_baku.kode_bahan, bahan_baku.satuan as satuan_dasar, users.nama_user')
            ->join('bahan_baku', 'bahan_baku.id_bahan = beli_bahan.id_bahan', 'left')
            ->join('users', 'users.id_user = beli_bahan.id_user', 'left');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('beli_bahan.kode_beli', $keyword)
                ->orLike('bahan_baku.nama_bahan', $keyword)
                ->groupEnd();
        }

        $builder->orderBy('beli_bahan.tanggal_beli', 'DESC');

        return $this->paginate($perPage);
    }
}