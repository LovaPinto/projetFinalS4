<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class V2AlterOperateurs extends Migration
{
    public function up()
    {
        $this->forge->addColumn('operateurs', [
            'commission_pct' => [
                'type'    => 'REAL',
                'default' => 2.0,
                'null'    => false,
            ],
            'est_principal' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null'    => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('operateurs', ['commission_pct', 'est_principal']);
    }
}
