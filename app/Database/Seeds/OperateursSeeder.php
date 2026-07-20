<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OperateursSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nom'           => 'Orange',
                'code'          => 'ORG',
                'actif'         => 1,
                'date_creation' => date('Y-m-d H:i:s'),
            ],
            [
                'nom'           => 'Airtel',
                'code'          => 'ATL',
                'actif'         => 1,
                'date_creation' => date('Y-m-d H:i:s'),
            ],
            [
                'nom'           => 'Yas',
                'code'          => 'YAS',
                'actif'         => 1,
                'date_creation' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('operateurs')->insertBatch($data);
    }
}