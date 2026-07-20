<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TypesOperation extends Seeder
{
    public function run()
    {
        $data = [
            ['code' => 'DEPOT',     'libelle' => 'Dépôt',     'actif' => 1],
            ['code' => 'RETRAIT',   'libelle' => 'Retrait',   'actif' => 1],
            ['code' => 'TRANSFERT', 'libelle' => 'Transfert', 'actif' => 1],
        ];

        $this->db->table('types_operation')->insertBatch($data);
    }
}
