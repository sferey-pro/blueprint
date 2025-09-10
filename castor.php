<?php

declare(strict_types=1);

use Castor\Attribute\{AsContext, AsTask};
use Castor\Context;
use Symfony\Component\Process\Process;
use function Castor\guard_min_version;
use function Castor\import;
use function utils\title;

guard_min_version('0.27.0');

import(__DIR__.'/.castor');

function create_default_variables(): array
{
    $projectName = 'blueprint';
    $serverName = 'localhost';

    return [
        'project_name' => $projectName,
        'server_name' => $serverName,
        'php_version' => 8.4,
        'symfony_version' => '7.3.*',
    ];
}

#[AsContext(default: true)]
function create_default_context(): Context
{
    $data = create_default_variables() + [
        'stability' => 'stable',
        'docker_compose_files' => [
            'compose.yaml',
        ],
        'root_dir' => '/app',
    ];

    if (file_exists(__DIR__.'/compose.override.yaml')) {
        $data['docker_compose_files'][] = 'compose.override.yaml';
    }

    return new Context(
        $data,
        pty: Process::isPtySupported(),
        environment: [
            'BUILDKIT_PROGRESS' => 'plain',
        ]
    );
}

#[AsTask(description: 'Install all tools dependencies')]
function install(): void
{
    title();

    \qa\cs\install();
    \qa\phpstan\install();
    \qa\deptrac\install();
    \qa\parallelLint\install();
}

#[AsTask(description: 'Update all tools dependencies')]
function update(): void
{
    title();

    \qa\cs\update();
    \qa\phpstan\update();
    \qa\deptrac\update();
    \qa\parallelLint\update();
}
