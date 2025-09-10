<?php

namespace qa\infection;

use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\io;
use function Castor\variable;
use function docker\docker_compose_run;
use function docker\docker_exit_code;
use function utils\title;

const TOOLS_DIR = '/tools/infection';

#[AsTask(name: 'run', description: 'Run infection', aliases: ['infection'])]
function infection(): int
{
    title();
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        install();
    }

    $command = [
        variable('root_dir') . TOOLS_DIR . '/vendor/bin/infection',
    ];

    return docker_exit_code($command);
}

#[AsTask(description: 'install dependencies')]
function install(): void
{
    io()->section('Install infection dependencies');
    docker_compose_run(['composer', 'install', '--no-scripts', '--working-dir='. variable('root_dir') . TOOLS_DIR], c: context());
}

#[AsTask(description: 'update dependencies')]
function update(): void
{
    io()->section('Update infection dependencies');
    docker_compose_run(['composer', 'update', '--no-scripts', '--working-dir='. variable('root_dir') . TOOLS_DIR], c: context());
}
