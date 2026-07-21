<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremesFraisModel extends Model
{
    protected $table            = 'baremes_frais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'type_operation_id',
        'operateur_id',
        'montant_min',
        'montant_max',
        'frais',
        'actif',
        'date_debut',
        'date_fin',
        'promotion'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Calcul des frais de transfert (payés par l'expéditeur).
     */
    public function calculerFraisTransfert(float $montant): float
    {
        $typeModel = new TypesOperationModel();
        $type      = $typeModel->where('code', 'TRANSFERT')->first();

        if (!$type) {
            return 0;
        }

        $bareme = $this->where('type_operation_id', $type['id'])
            ->where('actif', 1)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->where('date_fin IS NULL')
            ->where('operateur_id IS NULL')
            ->first();

        return $bareme ? (float) $bareme['frais'] : 0;
    }


    /**
     * Calcul des frais de retrait pour un opérateur donné.
     * Retourne 0 si l'opérateur n'a pas de frais de retrait configurés.
     */
    public function calculerFraisRetrait(int $operateurId, float $montant): float
    {
        $typeModel = new TypesOperationModel();
        $type      = $typeModel->where('code', 'RETRAIT')->first();

        if (!$type) {
            return 0;
        }

        $bareme = $this->where('type_operation_id', $type['id'])
            ->where('operateur_id', $operateurId)
            ->where('actif', 1)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->where('date_fin IS NULL')
            ->first();

        return $bareme ? (float) $bareme['frais'] : 0;
    }

    /**
     * Vérifie si un opérateur facture des frais de retrait.
     */
    public function operateurAFraisRetrait(int $operateurId): bool
    {
        $typeModel = new TypesOperationModel();
        $type      = $typeModel->where('code', 'RETRAIT')->first();

        if (!$type) {
            return false;
        }

        $count = $this->where('type_operation_id', $type['id'])
            ->where('operateur_id', $operateurId)
            ->where('actif', 1)
            ->where('date_fin IS NULL')
            ->countAllResults();

        return $count > 0;
    }

    /**
     * Calcul des frais de retrait pour un opérateur donné.
     * Retourne 0 si pas de frais configurés pour cet opérateur.
     */
    public function calculerFrais(string $codeType, float $montant): float
    {
        $typeModel = new TypesOperationModel();
        $type      = $typeModel->where('code', $codeType)->first();

        if (!$type) {
            return 0;
        }

        $bareme = $this->where('type_operation_id', $type['id'])
            ->where('actif', 1)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->where('date_fin IS NULL')
            ->first();

        return $bareme ? (float) $bareme['frais'] : 0;
    }
  public function calculerFraisPromotion(string $codeType, float $montant): float
    {
        $typeModel = new TypesOperationModel();
        $type      = $typeModel->where('code', $codeType)->first();

        if (!$type) {
            return 0;
        }

        $bareme = $this->where('type_operation_id', $type['id'])
            ->where('actif', 1)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->where('date_fin IS NULL')
            ->first();

        return $bareme ? (float) $bareme['promotion'] : 0;
    }
}
