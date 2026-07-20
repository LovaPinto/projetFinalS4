<?php
require __DIR__ . '/../vendor/autoload.php';
$db = \Config\Database::connect();
$db->table('operateurs')->where('nom', 'Orange')->update(['commission_pct' => 2.0, 'est_principal' => 1]);
$db->table('operateurs')->where('nom', 'Airtel')->update(['commission_pct' => 2.0, 'est_principal' => 0]);
$db->table('operateurs')->where('nom', 'Yas')->update(['commission_pct' => 2.5, 'est_principal' => 0]);
echo "Seed update done\n";
