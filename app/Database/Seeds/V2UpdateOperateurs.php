<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class V2UpdateOperateurs extends Seeder
{
    public function run()
    {
        $this->db->table('operateurs')->where('nom', 'Orange')->update(['commission_pct' => 2.0, 'est_principal' => 1]);
        $this->db->table('operateurs')->where('nom', 'Airtel')->update(['commission_pct' => 2.0, 'est_principal' => 0]);
        $this->db->table('operateurs')->where('nom', 'Yas')->update(['commission_pct' => 2.5, 'est_principal' => 0]);
    }
}
