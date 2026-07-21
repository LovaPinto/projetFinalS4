<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BaremesFrais extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            // RETRAIT - frais de retrait uniquement pour Yas (operateur_id = 3)
            ['type_operation_id' => 2, 'operateur_id' => 3, 'montant_min' => 100,   'montant_max' => 1000,   'frais' => 50,  'actif' => 1, 'date_debut' => $now, 'date_fin' => null ],
            ['type_operation_id' => 2, 'operateur_id' => 3, 'montant_min' => 1001,  'montant_max' => 5000,   'frais' => 50,  'actif' => 1, 'date_debut' => $now, 'date_fin' => null],
            ['type_operation_id' => 2, 'operateur_id' => 3, 'montant_min' => 5001,  'montant_max' => 10000,  'frais' => 100, 'actif' => 1, 'date_debut' => $now, 'date_fin' => null],
            ['type_operation_id' => 2, 'operateur_id' => 3, 'montant_min' => 10001, 'montant_max' => 25000,  'frais' => 200, 'actif' => 1, 'date_debut' => $now, 'date_fin' => null],
            ['type_operation_id' => 2, 'operateur_id' => 3, 'montant_min' => 25001, 'montant_max' => 50000,  'frais' => 400, 'actif' => 1, 'date_debut' => $now, 'date_fin' => null],

            // TRANSFERT - frais généraux (operateur_id = NULL)
            ['type_operation_id' => 3, 'operateur_id' => null, 'montant_min' => 100,   'montant_max' => 10000,  'frais' => 100, 'actif' => 1, 'date_debut' => $now, 'date_fin' => null ,    'promotion' => 1],
            ['type_operation_id' => 3, 'operateur_id' => null, 'montant_min' => 10001, 'montant_max' => 50000,  'frais' => 300, 'actif' => 1, 'date_debut' => $now, 'date_fin' => null, 'promotion'=>3],
        ];

        $this->db->table('baremes_frais')->insertBatch($data);
    }
}
