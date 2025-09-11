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

#[AsTask(namespace: 'tools', name: 'install',description: 'Install all tools dependencies')]
function installTools(): void
{
    title();

    \qa\cs\install();
    \qa\phpstan\install();
    \qa\deptrac\install();
    \qa\parallelLint\install();
}

#[AsTask(namespace: 'tools', name: 'update', description: 'Update all tools dependencies')]
function updateTools(): void
{
    title();

    \qa\cs\update();
    \qa\phpstan\update();
    \qa\deptrac\update();
    \qa\parallelLint\update();
}
