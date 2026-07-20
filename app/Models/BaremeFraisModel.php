<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
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

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'type_operation_id' => 'required|is_not_unique[types_operation.id]',
        'montant_min'       => 'required|decimal|greater_than_equal_to[0]',
        'montant_max'       => 'required|decimal|greater_than_equal_to[montant_min]',
        'frais'             => 'required|decimal|greater_than_equal_to[0]',
    ];

    /**
     * Vérifie si une tranche se chevauche avec une tranche active existante
     * pour le même type d'opération.
     */
    public function hasOverlap(int $typeOperationId, float $montantMin, float $montantMax, ?int $excludeId = null): bool
    {
        $builder = $this->builder();
        $builder->where('type_operation_id', $typeOperationId);
        $builder->where('actif', 1);
        $builder->where('montant_min <=', $montantMax);
        $builder->where('montant_max >=', $montantMin);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}
