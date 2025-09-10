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
use function qa\cs\cs;
use function qa\deptrac\deptrac;
use function qa\parallelLint\parallelLint;
use function qa\phpstan\phpstan;
use function utils\title;

import(__DIR__. '/../tools/deptrac/castor.php');
import(__DIR__. '/../tools/infection/castor.php');
import(__DIR__. '/../tools/php-cs-fixer/castor.php');
import(__DIR__. '/../tools/php-parallel-lint/castor.php');
import(__DIR__. '/../tools/phpstan/castor.php');

#[AsTask(name: 'all', namespace: 'qa', description: 'Lance tous les outils de qualité du code (lint, analyse, tests)')]
function all(): int
{
    $lint = qa_lint();
    $analyze = qa_analyze();

    $phpunit = phpunit();

    return max($lint, $analyze, $phpunit);
}

#[AsTask(name: 'lint', namespace: 'qa', description: 'Vérifie la syntaxe et le style du code')]
function qa_lint(): int
{
    return max(
        parallelLint(),
        cs(),
        docker_exec_exit_code('bin/console lint:twig templates/'),
        docker_exec_exit_code('bin/console lint:yaml --parse-tags config/'),
        docker_exec_exit_code('bin/console lint:container')
    );
}

#[AsTask(name: 'analyze', namespace: 'qa', description: 'Lance l\'analyse statique du code')]
function qa_analyze(): int
{
    return max(deptrac(), phpstan());
}

#[AsTask(namespace: 'test', description: 'Run all PHPUnit tests', aliases: ['test'])]
function phpunit(
    #[AsOption(description: 'Run a specific group of tests (e.g., unit, integration)')]
    ?string $group = null,
    #[AsOption(description: 'Enable code coverage')]
    bool $cover = false,
    #[AsOption(description: 'Run tests in CI mode (generates reports)')]
    bool $ci = false,
    #[AsArgument(name: 'args', description: 'Additional arguments to pass to PHPUnit')]
    array $args = [],
): int {

    title();
    $c = context()
        ->withEnvironment(['APP_ENV' => 'test']);

    $command = [
        'bin/phpunit',
        '--colors=always',
    ];

    if ($group) {
        $command[] = '--group=' . $group;
    }

    if ($cover) {
        $command[] = '--testdox-html=build/testdox.html';
        $command[] = '--coverage-html=build/coverage/coverage-html';
    }

    if ($ci) {
        $command[] = '--coverage-clover=build/coverage/clover.xml';
        $command[] = '--coverage-cobertura=build/coverage/cobertura.xml';
        $command[] = '--coverage-crap4j=build/coverage/crap4j.xml';
        $command[] = '--coverage-xml=build/coverage/coverage-xml';
        $command[] = '--log-junit=build/junit.xml';
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

    return docker_exit_code($command, $c);
}
