<?php

namespace dependencies;

use Castor\Attribute\AsTask;

use function Castor\io;
use function docker\docker_compose_run;

#[AsTask(description: 'Install composer dependencies')]
function install(): void
{
    io()->section('Installing PHP dependencies');
    docker_compose_run('composer install --prefer-dist --no-progress --no-interaction');
}

#[AsTask(description: 'Install importmap dependencies')]
function importmapInstall(): void
{
    io()->section('Installing importmap');
    docker_compose_run('bin/console importmap:install');
}

#[AsTask(description: 'Update composer dependencies')]
function update(): void
{
    io()->section('Updating PHP dependencies');
    docker_compose_run('composer update');
}

#[AsTask(description: 'Show outdated composer dependencies')]
function outdated(): void
{
    io()->section('Outdated PHP dependencies');
    docker_compose_run('composer outdated -D');
}


