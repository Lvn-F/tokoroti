<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Simplifies bahan_baku.satuan down to BASE/STORAGE units only.
 * Purchase-time units (kg, liter) are handled in beli_bahan and
 * converted into these base units before touching stok.
 *
 * gram -> for anything measured by weight
 * ml   -> for anything measured by volume
 * butir-> for anything counted per item (eggs, etc) - no conversion
 */
class AlterBahanBakuSatuan extends Migration
{
    public function up()
    {
        // Normalize any existing rows into the new base-unit vocabulary
        // BEFORE shrinking the enum, so no row ends up with an invalid value.
        $this->db->query("UPDATE bahan_baku SET satuan = 'gram' WHERE satuan = 'kg'");
        $this->db->query("UPDATE bahan_baku SET satuan = 'ml' WHERE satuan = 'liter'");
        $this->db->query("UPDATE bahan_baku SET satuan = 'butir' WHERE satuan IN ('pcs', 'sdt')");

        $this->forge->modifyColumn('bahan_baku', [
            'satuan' => [
                'name'       => 'satuan',
                'type'       => 'ENUM',
                'constraint' => ['gram', 'ml', 'butir'],
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('bahan_baku', [
            'satuan' => [
                'name'       => 'satuan',
                'type'       => 'ENUM',
                'constraint' => ['pcs', 'kg', 'gram', 'liter', 'ml'],
            ],
        ]);
    }
}