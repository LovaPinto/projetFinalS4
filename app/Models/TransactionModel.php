<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'reference',
        'type_operation_id',
        'client_source_id',
        'client_destination_id',
        'montant',
        'montant_principal',
        'montant_epargne',
        'frais',
        'montant_total',
        'commission',
        'type_transfert',
        'solde_avant',
        'solde_apres',
        'statut',
        'date_creation',
    ];

    /**
     * Historique des opérations d'un client, qu'il soit source ou destination,
     * avec le libellé du type d'opération, du plus récent au plus ancien.
     */
    public function getHistoriqueClient(int $clientId, int $limite = 20): array
    {
        return $this->select('transactions.*, types_operation.libelle AS type_libelle')
            ->join('types_operation', 'types_operation.id = transactions.type_operation_id')
            ->groupStart()
                ->where('client_source_id', $clientId)
                ->orWhere('client_destination_id', $clientId)
            ->groupEnd()
            ->orderBy('date_creation', 'DESC')
            ->findAll($limite);
    }
}
