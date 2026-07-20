<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrefixesOperateur extends Migration
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
            'operateur_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'prefixe' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
            ],
            'actif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'date_creation' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('prefixe');
        $this->forge->addForeignKey('operateur_id', 'operateurs', 'id', '', 'CASCADE');
        $this->forge->createTable('prefixes_operateur');
    }

    public function down()
    {
        $this->forge->dropTable('prefixes_operateur');
    }
}
