<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogActivityTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_log' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'aktivitas' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'modul' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id_log');
        $this->forge->addKey('id_user');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', '', 'CASCADE');
        $this->forge->createTable('log_activity', true);
    }

    public function down()
    {
        $this->forge->dropTable('log_activity', true);
    }
}