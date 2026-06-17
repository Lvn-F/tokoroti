<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKategoriProdukTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kategori' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'deskripsi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id_kategori');
        $this->forge->createTable('kategori_produk', true);
    }

    public function down()
    {
        $this->forge->dropTable('kategori_produk', true);
    }
}