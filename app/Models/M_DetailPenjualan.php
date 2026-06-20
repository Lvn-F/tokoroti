<?php

namespace App\Models;

use CodeIgniter\Model;

class M_DetailPenjualan extends Model
{
    protected $table            = 'detail_penjualan';
    protected $primaryKey       = 'id_detail_penjualan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_penjualan',
        'id_produk',
        'harga',
        'qty',
        'subtotal',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_penjualan' => 'required|integer',
        'id_produk'    => 'required|integer',
        'harga'        => 'required|decimal',
        'qty'          => 'required|integer|greater_than[0]',
        'subtotal'     => 'required|decimal',
    ];

    /**
     * Get all line items for a given penjualan, joined with produk
     * for display (nama_produk, kode_produk).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByPenjualan(int $idPenjualan): array
    {
        return $this->select('detail_penjualan.*, produk.nama_produk, produk.kode_produk, produk.gambar')
            ->join('produk', 'produk.id_produk = detail_penjualan.id_produk', 'left')
            ->where('detail_penjualan.id_penjualan', $idPenjualan)
            ->findAll();
    }

    /**
     * Bulk insert line items. Expects rows already shaped as
     * [id_penjualan, id_produk, harga, qty, subtotal].
     *
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