<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('OperateursSeeder');
        $this->call('PrefixesOperateurSeeder');
        $this->call('TypesOperationSeeder');
    }
}