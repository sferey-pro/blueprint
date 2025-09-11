<?php

declare(strict_types=1);

use function Castor\guard_min_version;
use function Castor\import;

guard_min_version('0.27.0');

import(__DIR__.'/.castor');

function create_default_variables(): array
{
    $projectName = 'blueprint';
    $serverName = 'localhost';

    return [
        'project_name' => $projectName,
        'server_name' => $serverName,
        'app_dir' => '/app',
        'php_version' => 8.4,
        'symfony_version' => '7.3.*',
    ];
}
