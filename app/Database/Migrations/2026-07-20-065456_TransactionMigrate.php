<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactions extends Migration
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
            'reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'type_operation_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'client_source_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'client_destination_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'montant' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
            ],
            'frais' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0,
            ],
            'montant_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
            ],
            'solde_avant' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
            ],
            'solde_apres' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
            ],
            'statut' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'REUSSI',
            ],
            'date_creation' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('reference');
        $this->forge->addForeignKey('type_operation_id', 'types_operation', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('client_source_id', 'clients', 'id', '', 'SET NULL');
        $this->forge->addForeignKey('client_destination_id', 'clients', 'id', '', 'SET NULL');
        $this->forge->createTable('transactions');
    }

    public function down()
    {
        $this->forge->dropTable('transactions');
    }
}
