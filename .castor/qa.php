<?php

namespace qa;

use Castor\Attribute\AsArgument;
use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\import;
use function Castor\io;
use function docker\docker_exit_code;
use function docker\docker_exec_exit_code;

import(__DIR__. '/../tools/deptrac/castor.php');
import(__DIR__. '/../tools/infection/castor.php');
import(__DIR__. '/../tools/php-cs-fixer/castor.php');
import(__DIR__. '/../tools/php-parallel-lint/castor.php');
import(__DIR__. '/../tools/phpstan/castor.php');

#[AsTask(name: 'all', namespace: 'qa', description: 'Lance tous les outils de qualité du code (lint, analyse, tests)')]
function all(): int
{
    $lint = lint();
    $analyse = analyse();

    $test = phpunit();

    return max($lint, $analyse, $test);
}

#[AsTask(name: 'lint', namespace: 'qa', description: 'Vérifie la syntaxe et le style du code')]
function lint(): int
{
    return max(
        parallelLint\run(),
        cs\run(),
        docker_exec_exit_code('bin/console lint:twig templates/'),
        docker_exec_exit_code('bin/console lint:yaml --parse-tags config/'),
        docker_exec_exit_code('bin/console lint:container')
    );
}

#[AsTask(name: 'analyse', namespace: 'qa', description: 'Lance les outils d\'analyse statique (phpstan, deptrac, infection)')]
function analyse(): int
{
    return max(
        phpstan\run(),
        deptrac\run(),
        infection\run()
    );
}


#[AsTask(namespace: 'qa', name: 'install',description: 'Install all Quality Assurance dependencies')]
function install(): void
{
    io()->title('Installing Quality Assurance dependencies');
    \qa\cs\install();
    \qa\phpstan\install();
    \qa\deptrac\install();
    \qa\parallelLint\install();
    \qa\infection\install();
}

#[AsTask(namespace: 'qa', name: 'update', description: 'Update all Quality Assurance dependencies')]
function update(): void
{
    io()->title('Updating Quality Assurance dependencies');
    \qa\cs\update();
    \qa\phpstan\update();
    \qa\deptrac\update();
    \qa\parallelLint\update();
    \qa\infection\update();
}

//

#[AsTask(namespace: 'qa', description: 'Run Deptrac', aliases: ['deptrac'])]
function deptrac(): int
{
    return deptrac\run();
}

#[AsTask(namespace: 'qa', description: 'Fixes Coding Style', aliases: ['cs'])]
function cs(bool $dryRun = false): int
{
    return cs\run($dryRun);
}

#[AsTask(namespace: 'qa', description: 'Run infection', aliases: ['infection'])]
function infection(): int
{
    return infection\run();
}

#[AsTask(namespace: 'qa', description: 'Lint the syntax of PHP files', aliases: ['plint'])]
function parallelLint(): int
{
    return parallelLint\run();
}

#[AsTask(namespace: 'qa', description: 'Run PHPStan', aliases: ['stan'])]
function phpstan(bool $generateBaseline = false): int
{
    return phpstan\run($generateBaseline);
}

#[AsTask(namespace: 'test', description: 'Run all PHPUnit tests', aliases: ['test'])]
function phpunit(
    #[AsOption(description: 'Run a specific group of tests (e.g., unit, integration)')]
    ?string $group = null,
    #[AsOption(description: 'Enable code coverage')]
    bool $cover = false,
    #[AsArgument(name: 'args', description: 'Additional arguments to pass to PHPUnit')]
    array $args = [],
): int {

    io()->title('Running tests');
    $c = context('test');

    $command = [
        'bin/phpunit',
        '--colors=always',
    ];

    if ($group) {
        $command[] = '--group=' . $group;
    }

    if ($cover) {
        $c = $c->withData([
            'docker_compose_run_environment' => [
                'APP_ENV' => 'test',
                'XDEBUG_MODE' => 'coverage',
            ]
        ]);
        $command[] = '--testdox-html=build/testdox.html';
        $command[] = '--coverage-html=build/coverage/coverage-html';
    }

    // Append any extra arguments from the command line
    if ($args) {
        $command = array_merge($command, $args);
    }

    // The test database is required for all groups except 'unit'
    if ('unit' !== $group) {
        io()->section('Setting up test database');
        db_setup_test();
    }

    return docker_exit_code($command, c: $c);
}
