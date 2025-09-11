<?php

namespace qa\phpstan;

use function Castor\context;
use function Castor\io;
use function Castor\variable;
use function docker\docker_compose_exec;
use function docker\docker_compose_run;
use function docker\docker_exec_exit_code;

const TOOLS_DIR = '/tools/phpstan';

function run(bool $generateBaseline = false): int
{
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        install();
    }

    $containerCacheFile = variable('app_dir') . '/var/cache/dev/App_KernelDevDebugContainer.xml';


    if (docker_exec_exit_code(sprintf('[ -f %s ]', $containerCacheFile), c: context()->withAllowFailure()->withQuiet()) !== 0) {
        io()->note('PHPStan needs the dev/debug cache. Generating it...');
        docker_compose_exec('bin/console cache:warmup');
    }

    $command = [
        variable('app_dir') . TOOLS_DIR . '/vendor/bin/phpstan',
        '--memory-limit=-1',
        '--ansi',
        '-v',
    ];

    if ($generateBaseline) {
        $command[] = '-b';
    }

    return docker_exec_exit_code($command);
}

function install(): void
{
    io()->section('Install phpstan dependencies');
    docker_compose_run(['composer', 'install', '--no-scripts', '--working-dir='. variable('app_dir') . TOOLS_DIR], c: context());

    io()->newLine();
}

function update(): void
{
    io()->section('Update phpstan dependencies');
    docker_compose_run(['composer', 'update', '--no-scripts', '--working-dir='. variable('app_dir') . TOOLS_DIR], c: context());

    io()->newLine();
}
