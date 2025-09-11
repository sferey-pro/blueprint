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
        'root_dir' => \dirname(__DIR__),
    ];

    if (file_exists($data['root_dir'] . '/compose.override.yaml')) {
        $data['docker_compose_files'][] = 'compose.override.yaml';
    }

    return new Context(
        $data,
        pty: Process::isPtySupported(),
        tty: Process::isTtySupported(),
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
