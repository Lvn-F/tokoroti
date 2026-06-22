<?php

namespace App\Models;

use CodeIgniter\Model;

class M_DetailProduksi extends Model
{
    protected $table            = 'detail_produksi';
    protected $primaryKey       = 'id_detail_produksi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_produksi',
        'id_bahan',
        'jumlah_terpakai',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_produksi'     => 'required|integer',
        'id_bahan'        => 'required|integer',
        'jumlah_terpakai' => 'required|decimal',
    ];

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getByProduksi(int $idProduksi): array
    {
        return $this->select('detail_produksi.*, bahan_baku.nama_bahan, bahan_baku.kode_bahan, bahan_baku.satuan')
            ->join('bahan_baku', 'bahan_baku.id_bahan = detail_produksi.id_bahan', 'left')
            ->where('detail_produksi.id_produksi', $idProduksi)
            ->findAll();
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    public function insertBatchRows(array $rows): bool
    {
        if (empty($rows)) {
            return true;
        }

        return (bool) $this->insertBatch($rows);
    }
}