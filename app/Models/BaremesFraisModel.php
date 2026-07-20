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
        'montant_min',
        'montant_max',
        'frais',
        'actif',
        'date_debut',
        'date_fin',
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
     * Calcule les frais pour un type d'opération donné et un montant.
     */
    public function calculerFrais(string $codeType, float $montant): float
    {
        $typeModel  = new TypesOperationModel();
        $type       = $typeModel->where('code', $codeType)->first();

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
}
