<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PrefixesOperateurSeeder extends Seeder
{
    public function run()
    {
        $operateurs = $this->db->table('operateurs')
            ->select('id, code')
            ->get()
            ->getResultArray();

        $idsParCode = array_column($operateurs, 'id', 'code');

        $prefixes = [
            ['prefixe' => '033', 'code' => 'OPM'],
            ['prefixe' => '037', 'code' => 'OPM'],
        ];

        $data = [];
        foreach ($prefixes as $p) {
            if (!isset($idsParCode[$p['code']])) {
                continue;
            }

            $data[] = [
                'operateur_id'  => $idsParCode[$p['code']],
                'prefixe'       => $p['prefixe'],
                'actif'         => 1,
                'date_creation' => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('prefixes_operateur')->insertBatch($data);
    }
}
