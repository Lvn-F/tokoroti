<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResepTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
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

        // Composite primary key — also serves as the unique constraint
        // that used to be enforced manually in the controller.
        $this->forge->addPrimaryKey(['id_produk', 'id_bahan']);
        $this->forge->addForeignKey('id_produk', 'produk', 'id_produk', '', 'CASCADE');
        $this->forge->addForeignKey('id_bahan', 'bahan_baku', 'id_bahan', '', 'CASCADE');
        $this->forge->createTable('resep', true);
    }

    public function down()
    {
        $this->forge->dropTable('resep', true);
    }
}