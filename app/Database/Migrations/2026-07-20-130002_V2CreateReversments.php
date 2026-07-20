<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class V2CreateReversments extends Migration
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
            'transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'operateur_source_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'operateur_dest_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'montant' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
            ],
            'statut' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'EN_ATTENTE',
            ],
            'date_creation' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'date_modification' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('transaction_id', 'transactions', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('operateur_source_id', 'operateurs', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('operateur_dest_id', 'operateurs', 'id', '', 'CASCADE');
        $this->forge->createTable('reversments');
    }

    public function down()
    {
        $this->forge->dropTable('reversments');
    }
}
