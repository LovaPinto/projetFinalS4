<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Transactions extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $typeDepot     = $this->db->table('types_operation')->where('code', 'DEPOT')->get()->getRowArray();
        $typeRetrait   = $this->db->table('types_operation')->where('code', 'RETRAIT')->get()->getRowArray();
        $typeTransfert = $this->db->table('types_operation')->where('code', 'TRANSFERT')->get()->getRowArray();

        if (!$typeDepot || !$typeRetrait || !$typeTransfert) {
            return;
        }

        $c1 = $this->db->table('clients')->where('numero_telephone', '0341234567')->get()->getRowArray();
        $c2 = $this->db->table('clients')->where('numero_telephone', '0327788990')->get()->getRowArray();

        if (!$c1 || !$c2) {
            return;
        }

        $tx = [
            [
                'reference'             => 'DEP-20260720-001',
                'type_operation_id'     => $typeDepot['id'],
                'client_source_id'      => null,
                'client_destination_id' => $c1['id'],
                'montant'               => 50000,
                'frais'                 => 0,
                'montant_total'         => 50000,
                'solde_avant'           => 0,
                'solde_apres'           => 50000,
                'statut'                => 'REUSSI',
                'date_creation'         => $now,
            ],
            [
                'reference'             => 'DEP-20260720-002',
                'type_operation_id'     => $typeDepot['id'],
                'client_source_id'      => null,
                'client_destination_id' => $c2['id'],
                'montant'               => 25000,
                'frais'                 => 0,
                'montant_total'         => 25000,
                'solde_avant'           => 0,
                'solde_apres'           => 25000,
                'statut'                => 'REUSSI',
                'date_creation'         => $now,
            ],
            [
                'reference'             => 'RET-20260720-003',
                'type_operation_id'     => $typeRetrait['id'],
                'client_source_id'      => $c1['id'],
                'client_destination_id' => null,
                'montant'               => 10000,
                'frais'                 => 100,
                'montant_total'         => 10100,
                'solde_avant'           => 50000,
                'solde_apres'           => 39900,
                'statut'                => 'REUSSI',
                'date_creation'         => $now,
            ],
            [
                'reference'             => 'TRA-20260720-004',
                'type_operation_id'     => $typeTransfert['id'],
                'client_source_id'      => $c1['id'],
                'client_destination_id' => $c2['id'],
                'montant'               => 5000,
                'frais'                 => 100,
                'montant_total'         => 5100,
                'solde_avant'           => 39900,
                'solde_apres'           => 34800,
                'statut'                => 'REUSSI',
                'date_creation'         => $now,
            ],
        ];

        $this->db->table('transactions')->insertBatch($tx);

        $this->db->table('clients')->where('id', $c1['id'])->update(['solde' => 34800]);
    }
}
