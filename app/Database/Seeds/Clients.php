<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Clients extends Seeder
{
    public function run()
    {
        // Récupère les id des opérateurs à partir de leur code (voir OperateursSeeder)
        $operateurs = $this->db->table('operateurs')
            ->select('id, code')
            ->get()
            ->getResultArray();

        $idsParCode = array_column($operateurs, 'id', 'code');

        $now = date('Y-m-d H:i:s');

        $clients = [
            ['numero' => '0341234567', 'code' => 'YAS', 'solde' => 50000],
            ['numero' => '0389876543', 'code' => 'YAS', 'solde' => 12500],
            ['numero' => '0331112233', 'code' => 'ATL', 'solde' => 75300],
            ['numero' => '0334455667', 'code' => 'ATL', 'solde' => 0],
            ['numero' => '0327788990', 'code' => 'ORG', 'solde' => 250000],
            ['numero' => '0321239876', 'code' => 'ORG', 'solde' => 3400],
        ];

        $data = [];
        foreach ($clients as $c) {
            if (!isset($idsParCode[$c['code']])) {
                continue; // opérateur non trouvé, on ignore plutôt que planter le seed
            }

            $data[] = [
                'numero_telephone'        => $c['numero'],
                'operateur_id'            => $idsParCode[$c['code']],
                'solde'                   => $c['solde'],
                'statut'                  => 'ACTIF',
                'date_creation'           => $now,
                'date_derniere_connexion' => $now,
            ];
        }

        $this->db->table('clients')->insertBatch($data);
    }
}