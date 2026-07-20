<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Clients extends Seeder
{
    public function run()
    {
        $operateurs = $this->db->table('operateurs')
            ->select('id, code')
            ->get()
            ->getResultArray();

        $idsParCode = array_column($operateurs, 'id', 'code');

        $now = date('Y-m-d H:i:s');

        $clients = [
            ['numero' => '0331234567', 'code' => 'OPM', 'solde' => 125000],
            ['numero' => '0379876543', 'code' => 'OPM', 'solde' => 70000],
        ];

        $data = [];
        foreach ($clients as $c) {
            if (!isset($idsParCode[$c['code']])) {
                continue;
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
