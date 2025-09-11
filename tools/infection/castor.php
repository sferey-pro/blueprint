<?php

namespace qa\infection;

use function Castor\context;
use function Castor\io;
use function Castor\variable;
use function docker\docker_compose_run;
use function docker\docker_exit_code;

const TOOLS_DIR = '/tools/infection';

function run(): int
{
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        install();
    }

    $command = [
        variable('app_dir') . TOOLS_DIR . '/vendor/bin/infection',
    ];

    return docker_exit_code($command);
}

function install(): void
{
    io()->section('Install infection dependencies');
    docker_compose_run(['composer', 'install', '--no-scripts', '--working-dir='. variable('app_dir') . TOOLS_DIR], c: context());

    io()->newLine();
}

function update(): void
{
    io()->section('Update infection dependencies');
    docker_compose_run(['composer', 'update', '--no-scripts', '--working-dir='. variable('app_dir') . TOOLS_DIR], c: context());

    io()->newLine();
}
