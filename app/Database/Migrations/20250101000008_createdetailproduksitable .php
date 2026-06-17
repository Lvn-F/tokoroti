<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailProduksiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail_produksi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_produksi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_bahan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jumlah_terpakai' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
        ]);

        $this->forge->addPrimaryKey('id_detail_produksi');
        $this->forge->addKey('id_produksi');
        $this->forge->addKey('id_bahan');
        $this->forge->addForeignKey('id_produksi', 'produksi', 'id_produksi', '', 'CASCADE');
        $this->forge->addForeignKey('id_bahan', 'bahan_baku', 'id_bahan', '', 'CASCADE');
        $this->forge->createTable('detail_produksi', true);
    }

    public function down()
    {
        $this->forge->dropTable('detail_produksi', true);
    }
}