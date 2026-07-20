<?php

namespace App\Models;

use CodeIgniter\Model;

class ReversmentModel extends Model
{
    protected $table            = 'reversments';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'transaction_id',
        'operateur_source_id',
        'operateur_dest_id',
        'montant',
        'statut',
        'date_creation',
        'date_modification',
    ];
}
