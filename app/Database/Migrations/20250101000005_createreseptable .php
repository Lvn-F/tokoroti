<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResepTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_resep' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_produk' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_bahan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jumlah_dibutuhkan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id_resep');
        $this->forge->addKey('id_produk');
        $this->forge->addKey('id_bahan');
        $this->forge->addForeignKey('id_produk', 'produk', 'id_produk', '', 'CASCADE');
        $this->forge->addForeignKey('id_bahan', 'bahan_baku', 'id_bahan', '', 'CASCADE');
        $this->forge->createTable('resep', true);
    }

    public function down()
    {
        $this->forge->dropTable('resep', true);
    }
}