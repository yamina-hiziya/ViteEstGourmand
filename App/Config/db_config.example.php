<?php

// Configuration de la base MySQL
// En local : les valeurs du service "db" de docker-compose.yml
return [
    'db_name' => 'vite_gourmand',
    'db_user' => 'vg_user',
    'db_password' => 'vg_pass',
    'db_port' => '3306',
    'db_host' => 'db',
];