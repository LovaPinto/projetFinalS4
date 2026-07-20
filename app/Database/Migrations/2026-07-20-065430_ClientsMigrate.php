<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClients extends Migration
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
            'numero_telephone' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
            ],
            'operateur_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'solde' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0,
            ],
            'statut' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'ACTIF',
            ],
            'date_creation' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'date_derniere_connexion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('numero_telephone');
        $this->forge->addForeignKey('operateur_id', 'operateurs', 'id', '', 'CASCADE');
        $this->forge->createTable('clients');
    }

    public function down()
    {
        $this->forge->dropTable('clients');
    }
}
