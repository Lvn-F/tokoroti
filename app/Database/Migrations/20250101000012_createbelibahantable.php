<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBeliBahanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_beli' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_beli' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'id_bahan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'satuan_beli' => [
                'type'       => 'ENUM',
                'constraint' => ['kg', 'gram', 'liter', 'ml', 'butir'],
            ],
            'jumlah_beli' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'total_harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'jumlah_konversi' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'harga_per_satuan' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,4',
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal_beli' => [
                'type' => 'DATE',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id_beli');
        $this->forge->addUniqueKey('kode_beli');
        $this->forge->addKey('id_bahan');
        $this->forge->addKey('id_user');
        $this->forge->addForeignKey('id_bahan', 'bahan_baku', 'id_bahan', '', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', '', 'CASCADE');
        $this->forge->createTable('beli_bahan', true);
    }

    public function down()
    {
        $this->forge->dropTable('beli_bahan', true);
    }
}