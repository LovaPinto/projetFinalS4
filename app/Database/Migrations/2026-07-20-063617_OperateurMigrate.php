<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperateurs extends Migration
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
            'nom' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'mot_de_passe' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addUniqueKey('nom');
        $this->forge->addUniqueKey('code');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('operateurs');
    }

    public function down()
    {
        $this->forge->dropTable('operateurs');
    }
}
