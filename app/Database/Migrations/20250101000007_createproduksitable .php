<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProduksiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_produksi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_produksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'id_produk' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jumlah_produksi' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'tanggal_produksi' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['proses', 'selesai', 'gagal'],
                'default'    => 'proses',
                'null'       => true,
            ],
            'catatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id_produksi');
        $this->forge->addUniqueKey('kode_produksi');
        $this->forge->addKey('id_produk');
        $this->forge->addKey('id_user');
        $this->forge->addForeignKey('id_produk', 'produk', 'id_produk', '', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', '', 'CASCADE');
        $this->forge->createTable('produksi', true);
    }

    public function down()
    {
        $this->forge->dropTable('produksi', true);
    }
}