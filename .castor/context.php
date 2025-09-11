<?php

namespace docker;

use Castor\Attribute\AsContext;
use Castor\Context;
use Symfony\Component\Process\Process;

#[AsContext(default: true)]
function create_default_context(): Context
{
    $data = create_default_variables() + [
        'stability' => 'stable',
        'docker_compose_files' => [
            'compose.yaml',
        ],
        'docker_compose_run_environment' => [],
        'root_dir' => '/app',
    ];

    if (file_exists(dirname(__DIR__, 1) . '/compose.override.yaml')) {
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

#[AsContext(name: 'test')]
function create_test_context(): Context
{
    $c = create_default_context();

    return $c
        ->withData([
            'docker_compose_run_environment' => [
                'APP_ENV' => 'test',
            ],
        ])
    ;
}

#[AsContext(name: 'ci')]
function create_ci_context(): Context
{
    $c = create_test_context();

    return $c
        ->withData([])
        ->withEnvironment([
            'COMPOSE_ANSI' => 'never',
        ])
    ;
}
