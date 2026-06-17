<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenjualanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_penjualan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_penjualan' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'no_invoice' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'nama_customer' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'telepon_customer' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'id_kasir' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'total_harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'metode_bayar' => [
                'type'       => 'ENUM',
                'constraint' => ['cash', 'debit', 'credit', 'qris'],
            ],
            'bayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'kembalian' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'completed', 'cancel'],
                'default'    => 'pending',
                'null'       => true,
            ],
            'tanggal_penjualan' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id_penjualan');
        $this->forge->addUniqueKey('kode_penjualan');
        $this->forge->addUniqueKey('no_invoice');
        $this->forge->addKey('id_kasir');
        $this->forge->addForeignKey('id_kasir', 'users', 'id_user', '', 'CASCADE');
        $this->forge->createTable('penjualan', true);
    }

    public function down()
    {
        $this->forge->dropTable('penjualan', true);
    }
}