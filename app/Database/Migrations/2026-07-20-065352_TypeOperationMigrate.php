<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTypesOperation extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'libelle' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'avec_frais' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'actif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('types_operation');
    }

    public function down()
    {
        $this->forge->dropTable('types_operation');
    }
}
