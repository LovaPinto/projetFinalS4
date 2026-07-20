<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OperateursSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nom'           => 'Opérateur Mobile Money',
                'code'          => 'OPM',
                'email'         => 'admin@mobile.mg',
                'mot_de_passe'  => password_hash('admin123', PASSWORD_DEFAULT),
                'actif'         => 1,
                'date_creation' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('operateurs')->insertBatch($data);
    }
}
