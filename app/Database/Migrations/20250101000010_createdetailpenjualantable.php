<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailPenjualanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail_penjualan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_penjualan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_produk' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'qty' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
        ]);

        $this->forge->addPrimaryKey('id_detail_penjualan');
        $this->forge->addKey('id_penjualan');
        $this->forge->addKey('id_produk');
        $this->forge->addForeignKey('id_penjualan', 'penjualan', 'id_penjualan', '', 'CASCADE');
        $this->forge->addForeignKey('id_produk', 'produk', 'id_produk', '', 'CASCADE');
        $this->forge->createTable('detail_penjualan', true);
    }

    public function down()
    {
        $this->forge->dropTable('detail_penjualan', true);
    }
}