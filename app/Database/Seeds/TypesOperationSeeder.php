<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TypesOperationSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['code' => 'DEPOT', 'libelle' => 'Dépôt', 'avec_frais' => 0, 'actif' => 1],
            ['code' => 'RETRAIT', 'libelle' => 'Retrait', 'avec_frais' => 1, 'actif' => 1],
            ['code' => 'TRANSFERT', 'libelle' => 'Transfert', 'avec_frais' => 1, 'actif' => 1],
        ];

        $this->db->table('types_operation')->insertBatch($data);
    }
}
