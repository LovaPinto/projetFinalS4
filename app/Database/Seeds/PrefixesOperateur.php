<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PrefixesOperateurSeeder extends Seeder
{
    public function run()
    {
        // Récupère les id des opérateurs à partir de leur code (voir OperateursSeeder)
        $operateurs = $this->db->table('operateurs')
            ->select('id, code')
            ->get()
            ->getResultArray();

        $idsParCode = array_column($operateurs, 'id', 'code');

        $prefixes = [
            ['prefixe' => '034', 'code' => 'YAS'],
            ['prefixe' => '038', 'code' => 'YAS'],
            ['prefixe' => '033', 'code' => 'ATL'],
            ['prefixe' => '032', 'code' => 'ORG'],
        ];

        $data = [];
        foreach ($prefixes as $p) {
            if (!isset($idsParCode[$p['code']])) {
                continue; // opérateur non trouvé, on ignore plutôt que planter le seed
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