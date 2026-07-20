<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'reference',
        'type_operation_id',
        'client_source_id',
        'client_destination_id',
        'montant',
        'frais',
        'montant_total',
        'solde_avant',
        'solde_apres',
        'statut',
        'date_creation',
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'reference'    => 'required|max_length[30]|is_unique[transactions.reference,id,{id}]',
        'type_operation_id' => 'required|is_not_unique[types_operation.id]',
        'montant'      => 'required|decimal|greater_than[0]',
        'frais'        => 'required|decimal|greater_than_equal_to[0]',
        'montant_total'=> 'required|decimal|greater_than[0]',
        'solde_avant'  => 'required|decimal',
        'solde_apres'  => 'required|decimal',
        'statut'       => 'required|in_list[REUSSI,ECHEC,ANNULE]',
    ];

    /**
     * Retourne l'historique des opérations d'un client
     * (qu'il soit la source ou la destination).
     */
    public function getHistoriqueClient(int $clientId, ?string $typeCode = null, ?string $dateStart = null, ?string $dateEnd = null, int $perPage = 10, int $page = 1): array
    {
        $builder = $this->builder();
        $builder->select('transactions.*, types_operation.libelle AS type_libelle, types_operation.code AS type_code, src.numero_telephone AS source_numero, dst.numero_telephone AS destination_numero');
        $builder->join('types_operation', 'types_operation.id = transactions.type_operation_id');
        $builder->join('clients AS src', 'src.id = transactions.client_source_id', 'left');
        $builder->join('clients AS dst', 'dst.id = transactions.client_destination_id', 'left');
        $builder->groupStart();
            $builder->where('transactions.client_source_id', $clientId);
            $builder->orWhere('transactions.client_destination_id', $clientId);
        $builder->groupEnd();

        if ($typeCode !== null && $typeCode !== '') {
            $builder->where('types_operation.code', $typeCode);
        }
        if ($dateStart !== null && $dateStart !== '') {
            $builder->where('transactions.date_creation >=', $dateStart . ' 00:00:00');
        }
        if ($dateEnd !== null && $dateEnd !== '') {
            $builder->where('transactions.date_creation <=', $dateEnd . ' 23:59:59');
        }

        $total = $builder->countAllResults(false);

        $offset = ($page - 1) * $perPage;
        $builder->orderBy('transactions.date_creation', 'DESC');
        $builder->limit($perPage, $offset);
        $results = $builder->get()->getResultArray();

        return [
            'data'       => $results,
            'total'      => $total,
            'perPage'    => $perPage,
            'currentPage'=> $page,
            'lastPage'   => (int) ceil($total / $perPage),
        ];
    }
}
