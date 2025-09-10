<?php

namespace qa\parallelLint;

use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\io;
use function Castor\variable;
use function docker\docker_compose_run;
use function docker\docker_exit_code;
use function utils\title;

const TOOLS_DIR = '/tools/php-parallel-lint';

#[AsTask(name: 'run', description: 'Lint the syntax of PHP files', aliases: ['plint'])]
function parallelLint(): int
{
    title();
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        install();
    }

    $command = [
        variable('root_dir') . TOOLS_DIR . '/vendor/bin/parallel-lint',
        '--colors',
        'src',
        'tests',
    ];

    return docker_exit_code($command);
}

#[AsTask(description: 'install dependencies')]
function install(): void
{
    io()->section('Install php-parallel-lint dependencies');
    docker_compose_run(['composer', 'install', '--no-scripts', '--working-dir='. variable('root_dir') . TOOLS_DIR], c: context());
}

#[AsTask(description: 'update dependencies')]
function update(): void
{
    io()->section('Update php-parallel-lint dependencies');
    docker_compose_run(['composer', 'update', '--no-scripts', '--working-dir='. variable('root_dir') . TOOLS_DIR], c: context());
}
