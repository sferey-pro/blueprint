<?php

namespace docker;

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use Castor\Context;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ExceptionInterface;

use function Castor\context;
use function Castor\io;
use function Castor\run;
use function Castor\variable;

#[AsTask(description: 'Build or rebuild services', aliases: ['build'])]
function build(
    #[AsOption(description: 'The service to build (default: all services)')]
    ?string $service = null
): void {
    io()->section(($service !== null) ? 'Building ' . $service : 'Building infrastructure');

    $command = [];

    $command = [
        ...$command,
        'build',
        '--no-cache',
        '--build-arg', 'PHP_VERSION=' . variable('php_version'),
        '--build-arg', 'PROJECT_NAME=' . variable('project_name'),
    ];

    if ($service) {
        $command[] = $service;
    }

    docker_compose($command);
}

#[AsTask(description: 'Starts the infrastructure', aliases: ['up'])]
function up(
    #[AsOption(description: 'The service to start (default: all services)')]
    ?string $service = null,
): void {
    io()->section(($service !== null) ? 'Starting ' . $service : 'Starting infrastructure');

    $command = ['up', '--detach', '--no-build'];

    if ($service) {
        $command[] = $service;
    }

    try {
        docker_compose($command);
    } catch (ExceptionInterface $e) {
        io()->error('An error occurred while starting the infrastructure.');
        io()->note('Did you forget to run "castor docker:build"?');
        io()->note('Or you forget to login to the registry?');

        throw $e;
    }
}

#[AsTask(description: 'Stops the infrastructure', aliases: ['stop'])]
function stop(
    #[AsOption(description: 'The service to stop (default: all services)')]
    ?string $service = null,
): void {
    io()->section(($service !== null) ? 'Stopping ' . $service : 'Stopping infrastructure');

    $command = ['stop'];

    if ($service) {
        $command[] = $service;
    }

    docker_compose($command);
}

#[AsTask(description: 'Cleans the infrastructure (remove container, volume, networks)', aliases: ['destroy'])]
function destroy(
    #[AsOption(description: 'Force the destruction without confirmation', shortcut: 'f')]
    bool $force = false,
): void {
    io()->section('Destroying infrastructure');

    if (!$force) {
        io()->warning('This will permanently remove all containers, volumes, networks... created for this project.');
        io()->note('You can use the --force option to avoid this confirmation.');
        if (!io()->confirm('Are you sure?', false)) {
            io()->comment('Aborted.');

            return;
        }
    }

    docker_compose(['down', '--remove-orphans', '--volumes', '--rmi=local']);
}


function docker_compose(
    array $subCommand,
    ?Context $c = null
): Process {
    $c ??= context();

    docker_health_check($c);

    $c = $c->withEnvironment([
        'PROJECT_NAME' => $c['project_name'],
        'PHP_VERSION' => $c['php_version']
    ]);

    $command = [
        'docker',
        'compose',
        '-p', $c['project_name'],
    ];

    foreach ($c['docker_compose_files'] as $file) {
        $command[] = '-f';
        $command[] = $c->workingDirectory . '/' . $file;
    }

    $command = array_merge($command, $subCommand);

    return run($command, context: $c);
}

function docker_compose_run(
    string|array $runCommand,
    ?Context $c = null,
    string $service = 'php',
    bool $noDeps = true,
    ?string $workDir = null,
    bool $portMapping = false
): Process {
    $c ??= context();

    if(is_array($runCommand)) {
        $runCommand = implode(" ", $runCommand);
    }

    $command = [
        'run',
        '--rm',
    ];

    if ($noDeps) {
        $command[] = '--no-deps';
    }

    if ($portMapping) {
        $command[] = '--service-ports';
    }

    if (null !== $workDir) {
        $command[] = '--workdir';
        $command[] = $workDir;
    }

    foreach ($c['docker_compose_run_environment'] as $key => $value) {
        $command[] = '--env';
        $command[] = "{$key}={$value}";
    }

    $command[] = $service;
    $command[] = '/bin/bash';
    $command[] = '-c';
    $command[] = "exec {$runCommand}";

    return docker_compose($command, c: $c);
}

function docker_compose_exec(
    string|array $execCommand,
    ?Context $c = null,
    string $service = 'php',
    ?string $workDir = null
): Process {
    $c ??= context();

    if (is_array($execCommand)) {
        $execCommand = implode(' ', $execCommand);
    }

    $command = [
        'exec',
    ];

    if (null !== $workDir) {
        $command[] = '--workdir';
        $command[] = $workDir;
    }

    foreach ($c['docker_compose_run_environment'] as $key => $value) {
        $command[] = '--env';
        $command[] = "{$key}={$value}";
    }

    $command[] = $service;
    $command[] = '/bin/bash';
    $command[] = '-c';
    $command[] = "{$execCommand}";

    return docker_compose($command, c: $c);
}

function docker_exit_code(
    string|array $runCommand,
    ?Context $c = null,
    string $service = 'php',
    bool $noDeps = true,
    ?string $workDir = null
): int {
    $c = ($c ?? context())->withAllowFailure();

    $process = docker_compose_run(
        runCommand: $runCommand,
        c: $c,
        service: $service,
        noDeps: $noDeps,
        workDir: $workDir
    );

    return $process->getExitCode() ?? 0;
}

function docker_exec_exit_code(
    string|array $execCommand,
    ?Context $c = null,
    string $service = 'php',
    ?string $workDir = null
): int {
    $c = ($c ?? context())->withAllowFailure();

    $process = docker_compose_exec(
        execCommand: $execCommand,
        c: $c,
        service: $service,
        workDir: $workDir
    );

    return $process->getExitCode() ?? 0;
}

function docker_health_check(?Context $c = null)
{
    $c ??= context();

    $c = $c
        ->withAllowFailure(true)
        ->withQuiet(true)
    ;

    $process = run('docker --version', context: $c);

    if(!$process->isSuccessful()) {
        io()->error('Docker is not running');
        die;
    }
}
