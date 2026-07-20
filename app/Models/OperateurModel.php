<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table            = 'operateurs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'nom',
        'code',
        'email',
        'mot_de_passe',
        'actif',
        'date_creation',
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nom'    => 'required|max_length[100]',
        'code'   => 'required|max_length[10]',
        'email'  => 'required|valid_email|is_unique[operateurs.email,id,{id}]',
    ];
}
