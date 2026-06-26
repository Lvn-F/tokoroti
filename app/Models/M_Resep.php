<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Resep extends Model
{
    protected $table          = 'resep';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields  = true;

    protected $allowedFields = [
        'id_produk',
        'id_bahan',
        'jumlah_dibutuhkan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules = [
        'id_produk'         => 'required|integer',
        'id_bahan'          => 'required|integer',
        'jumlah_dibutuhkan' => 'required|decimal|greater_than[0]',
    ];

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getByProduk(int $idProduk): array
    {
        return $this->select('resep.*, bahan_baku.nama_bahan, bahan_baku.kode_bahan, bahan_baku.satuan, bahan_baku.stok, bahan_baku.harga_beli')
            ->join('bahan_baku', 'bahan_baku.id_bahan = resep.id_bahan', 'left')
            ->where('resep.id_produk', $idProduk)
            ->findAll();
    }

    /**
     * Find a single resep row by its composite key.
     *
     * @return array<string, mixed>|null
     */
    public function findByKeys(int $idProduk, int $idBahan): ?array
    {
        return $this->where('id_produk', $idProduk)
            ->where('id_bahan', $idBahan)
            ->first();
    }

    /**
     * Delete a resep row by its composite key.
     */
    public function deleteByKeys(int $idProduk, int $idBahan): bool
    {
        return $this->where('id_produk', $idProduk)
            ->where('id_bahan', $idBahan)
            ->delete();
    }

    /**
     * Estimated cost-of-goods (HPP) for ONE unit of a single produk,
     * calculated live as SUM(jumlah_dibutuhkan * harga_beli) across
     * all ingredients in its recipe.
     */
    public function getTotalBiayaByProduk(int $idProduk): float
    {
        $result = $this->select('SUM(resep.jumlah_dibutuhkan * bahan_baku.harga_beli) as total_biaya')
            ->join('bahan_baku', 'bahan_baku.id_bahan = resep.id_bahan', 'left')
            ->where('resep.id_produk', $idProduk)
            ->first();

        return (float) ($result['total_biaya'] ?? 0);
    }

    /**
     * Same as getTotalBiayaByProduk(), but batched for a list of produk
     * ids in a single query — use this on listing pages to avoid N+1.
     *
     * @param list<int> $idProdukList
     *
     * @return array<int, float> Keyed by id_produk.
     */
    public function getTotalBiayaForProduks(array $idProdukList): array
    {
        if (empty($idProdukList)) {
            return [];
        }

        $rows = $this->select('resep.id_produk, SUM(resep.jumlah_dibutuhkan * bahan_baku.harga_beli) as total_biaya')
            ->join('bahan_baku', 'bahan_baku.id_bahan = resep.id_bahan', 'left')
            ->whereIn('resep.id_produk', $idProdukList)
            ->groupBy('resep.id_produk')
            ->findAll();

        $map = [];

        foreach ($rows as $r) {
            $map[(int) $r['id_produk']] = (float) $r['total_biaya'];
        }

        return $map;
    }
}