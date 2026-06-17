<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBahanBakuTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bahan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_bahan' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'nama_bahan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'satuan' => [
                'type'       => 'ENUM',
                'constraint' => ['pcs', 'kg', 'gram', 'liter', 'ml'],
            ],
            'stok' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'stok_minimum' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
                'null'       => true,
            ],
            'harga_beli' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id_bahan');
        $this->forge->addUniqueKey('kode_bahan');
        $this->forge->createTable('bahan_baku', true);
    }

    public function down()
    {
        $this->forge->dropTable('bahan_baku', true);
    }
}