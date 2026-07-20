<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class V2AlterTransactions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transactions', [
            'commission' => [
                'type'    => 'DECIMAL',
                'constraint' => '14,2',
                'default' => 0,
                'null'    => false,
            ],
            'type_transfert' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => null,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', ['commission', 'type_transfert']);
    }
}
