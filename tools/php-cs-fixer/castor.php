<?php

namespace qa\cs;

use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\io;
use function Castor\variable;
use function docker\docker_compose_run;
use function docker\docker_exec_exit_code;
use function docker\docker_exit_code;
use function utils\title;

const TOOLS_DIR = '/tools/php-cs-fixer';

#[AsTask(name: 'run', description: 'Fixes Coding Style', aliases: ['cs'])]
function cs(bool $dryRun = false): int
{
    title();
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        install();
    }

    $command = [
        variable('root_dir') . TOOLS_DIR . '/vendor/bin/php-cs-fixer',
        'fix',
    ];

    if ($dryRun) {
        $command[] = '--dry-run';
    }

    return docker_exec_exit_code($command);
}

#[AsTask(description: 'install dependencies')]
function install(): void
{
    io()->section('Install php-cs-fixer dependencies');
    docker_compose_run(['composer', 'install', '--no-scripts', '--working-dir='. variable('root_dir') . TOOLS_DIR], c: context());
}

#[AsTask(description: 'update dependencies')]
function update(): void
{
    io()->section('Update php-cs-fixer dependencies');
    docker_compose_run(['composer', 'update', '--no-scripts', '--working-dir='. variable('root_dir') . TOOLS_DIR], c: context());
}
