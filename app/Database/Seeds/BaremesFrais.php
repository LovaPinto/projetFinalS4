<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BaremesFrais extends Seeder
{
    public function run()
    {
        $types = $this->db->table('types_operation')
            ->select('id, code')
            ->get()
            ->getResultArray();

        $idsParCode = array_column($types, 'id', 'code');

        $now = date('Y-m-d H:i:s');

        $tranches = [
            [100, 1000, 50],
            [1001, 5000, 50],
            [5001, 10000, 100],
            [10001, 25000, 200],
            [25001, 50000, 400],
            [50001, 100000, 800],
            [100001, 250000, 1500],
            [250001, 500000, 1500],
            [500001, 1000000, 2500],
            [1000001, 2000000, 3000],
        ];

        $data = [];
        foreach (['RETRAIT', 'TRANSFERT'] as $code) {
            if (!isset($idsParCode[$code])) {
                continue;
            }
            foreach ($tranches as [$min, $max, $frais]) {
                $data[] = [
                    'type_operation_id' => $idsParCode[$code],
                    'montant_min'       => $min,
                    'montant_max'       => $max,
                    'frais'             => $frais,
                    'actif'             => 1,
                    'date_debut'        => $now,
                    'date_fin'          => null,
                ];
            }
        }

        $this->db->table('baremes_frais')->insertBatch($data);
    }
}
