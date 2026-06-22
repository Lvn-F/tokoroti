<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Resep extends Model
{
    protected $table            = 'resep';
    protected $primaryKey       = 'id_resep';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

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
}