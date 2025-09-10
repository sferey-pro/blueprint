<?php

namespace composer;

use Castor\Attribute\AsTask;
use function docker\docker_compose_run;
use function utils\title;

#[AsTask(namespace: 'composer', description: 'Install composer dependencies')]
function install(): void
{
    title();
    docker_compose_run('composer install');
}

#[AsTask(namespace: 'composer', description: 'Update composer dependencies')]
function update(): void
{
    title();
    docker_compose_run('composer update');
}

#[AsTask(namespace: 'composer', description: 'Show outdated composer dependencies')]
function outdated(): void
{
    title();
    docker_compose_run('composer outdated -D');
}
