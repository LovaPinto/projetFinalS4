<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AppelToutes extends Seeder
{
    public function run()
    {
        $this->call('OperateursSeeder');
        $this->call('PrefixesOperateurSeeder');
        $this->call('TypesOperationSeeder');
        $this->call('BaremesFrais');
        $this->call('Clients');
    }
}
