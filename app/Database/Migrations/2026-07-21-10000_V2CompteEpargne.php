<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class V2CompteEpargne extends Migration

{
    public function up()
    {
           $this->forge->addColumn('clients', [
            'solde_epargne' => [
                'type'    => 'DECIMAL',
                'contraint' => '15,2',
                'default'    => 0,
                'null' => false,
                'after' => 'solde',
            ],
        ]);

          $this->forge->addColumn('transactions', [
            'montant_principal' => [
                'type'    => 'DECIMAL',
                'contraint' => '15,2',
                'default'    => 0,
                'null' => false,
                'after' => 'montant',
            ],
        ]);

         $this->forge->dropColumn('transactions', [
            'montant_principal',
            'montant_epargne'
            
        ]);
        $this->forge->dropColumn('clients', 'solde_eparge');
        
     }
          
}
