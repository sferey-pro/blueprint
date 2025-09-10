<?php

use Castor\Attribute\AsTask;
use Symfony\Component\Console\Helper\ProgressIndicator;

use function Castor\context;
use function Castor\fs;
use function Castor\io;
use function Castor\output;
use function docker\docker_compose;
use function docker\docker_compose_exec;
use function docker\docker_compose_run;
use function utils\aborted;
use function utils\success;
use function utils\title;

#[AsTask(namespace: 'symfony', description: 'Serve the application', aliases: ['start'])]
function start(): void
{
    title();

    $progressIndicator = new ProgressIndicator(output(), finishedIndicatorValue: '✅');
    $progressIndicator->start('Processing...');

    try {
        docker_compose(['up', '-d', '--build'], c: context()->withQuiet(true));
        $progressIndicator->finish('Finished');
    } catch (\Exception) {
        $progressIndicator->finish('Failed', '🚨');
    }

    success(0);
}

#[AsTask(namespace: 'symfony', description: 'Stop application', aliases: ['stop'])]
function stop(): void
{
    title();

    $progressIndicator = new ProgressIndicator(output(), finishedIndicatorValue: '✅');
    $progressIndicator->start('Processing...');

    try {
        docker_compose(['down', '--remove-orphans'], c: context()->withQuiet(true));
        $progressIndicator->finish('Finished');
    } catch (\Exception) {
        $progressIndicator->finish('Failed', '🚨');
    }

    success(0);
}

#[AsTask(namespace: 'symfony', description: 'Reload all assets', aliases: ['assets'])]
function assets(bool $watch = false): void
{
    title();
    $command = [
        'bin/console',
        'tailwind:build',
    ];

    if ($watch) {
        $command[] = '--watch';
    }

    docker_compose_run($command);

    success(0);
}

#[AsTask(namespace: 'symfony', description: 'Connect to the FrankenPHP container', aliases: ['bash'])]
function bash(): void
{
    title();
    $c = context()
        ->withTimeout(null)
        ->withTty()
        ->withAllowFailure()
    ;

    docker_compose_run('bash', c: $c);
}

#[AsTask(namespace: 'symfony', description: 'Purge all Symfony cache and logs', aliases: ['purge'])]
function purge(): void
{
    title();
    if (!io()->confirm('Are you sure?', false)) {
        aborted();
        return;
    }

    fs()->remove('./public/assets/');
    fs()->remove('./var/cache/');
    fs()->remove('./var/logs/');
    fs()->remove('./build/coverage/coverage-html');

    success(0);
}

#[AsTask(namespace: 'database', description: 'Reload all data in database', aliases: ['db:reload'])]
function reload(): void
{
    title();
    docker_compose_run('bin/console doctrine:database:drop --force --if-exists');
    docker_compose_run('bin/console doctrine:database:create --if-not-exists');
    docker_compose_run('bin/console doctrine:migrations:migrate --no-interaction');

    success(0);
}

#[AsTask(namespace: 'database', description: 'Load data fixtures', aliases: ['db:seed'])]
function seed(): void
{
    title();
    io()->info('Loading fixtures...');
    docker_compose_exec('bin/console doctrine:fixtures:load --no-interaction');

    success(0);
}

#[AsTask(name: 'setup-test', namespace: 'database', description: 'Prépare la base de données de test')]
function db_setup_test(): void
{
    docker_compose_exec('bin/console doctrine:database:drop --force --env=test --if-exists');
    docker_compose_exec('bin/console doctrine:database:create --env=test --if-not-exists');
    docker_compose_exec('bin/console doctrine:migrations:migrate -n --env=test');
}


#[AsTask(namespace: 'symfony', description: 'Switch to the production environment', aliases: ['prod'])]
function prod(): void
{
    title();
    if (io()->confirm('Are you sure you want to switch to the production environment? This will overwrite your .env.local file.', false)) {
        fs()->copy('.env.local.dist', '.env.local');
        docker_compose_run('bin/console tailwind:build --minify');
        docker_compose_run('bin/console asset-map:compile');
        success(0);

        return;
    }

    aborted();
}

#[AsTask(namespace: 'symfony', description: 'Switch to the development environment', aliases: ['dev'])]
function dev(): void
{
    title();
    if (io()->confirm('Are you sure you want to switch to the development environment? This will delete your .env.local file.', false)) {
        fs()->remove('.env.local');
        fs()->remove('./public/assets/');
        success(0);

        return;
    }

    aborted();
}



#[AsTask(namespace: 'cache', description: 'Clear Symfony cache')]
function clear(): void
{
    title();
    docker_compose_exec('bin/console cache:clear');
    success(0);
}

#[AsTask(namespace: 'log', description: 'Tail Symfony logs')]
function tail(): void
{
    title();
    $c = context()->withTty()->withTimeout(null);
    docker_compose_exec('tail -f var/log/dev.log', c: $c);
}
