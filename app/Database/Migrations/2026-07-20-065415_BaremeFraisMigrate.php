<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBaremesFrais extends Migration
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
            'type_operation_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'montant_min' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
            ],
            'montant_max' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
            ],
            'frais' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
            ],
            'actif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'date_debut' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'date_fin' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('type_operation_id', 'types_operation', 'id', '', 'CASCADE');
        $this->forge->createTable('baremes_frais');
    }

    public function down()
    {
        $this->forge->dropTable('baremes_frais');
    }
}
