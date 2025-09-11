<?php

use Castor\Attribute\AsTask;
use Symfony\Component\Console\Helper\ProgressIndicator;

use function Castor\context;
use function Castor\fs;
use function Castor\io;
use function Castor\variable;
use function docker\up as docker_up;
use function docker\stop as docker_stop;
use function docker\build as docker_build;
use function docker\docker_compose_exec;
use function docker\docker_compose_run;


#[AsTask(namespace: 'symfony', description: 'Serve the application', aliases: ['start'])]
function start()
{
    io()->title('Starting the application');

    docker_build();
    install();
    docker_up();

    io()->success('Done!');
}

#[AsTask(namespace: 'symfony', description: 'Stop application', aliases: ['stop'])]
function stop(): void
{
    io()->title('Stopping the application');

    docker_stop();

    io()->success('Done!');
}

#[AsTask(description: 'Installs the application (composer, yarn, ...)', namespace: 'app', aliases: ['install'])]
function install(): void
{
    io()->title('Installing the application');

    $basePath = sprintf('%s', variable('root_dir'));

    if (is_file("{$basePath}/composer.json")) {
        dependencies\install();
    }

    if (is_file("{$basePath}/importmap.php")) {
        dependencies\importmapInstall();
    }

    qa\install();

    io()->success('Done!');
}

#[AsTask(namespace: 'symfony', description: 'Reload all assets', aliases: ['assets'])]
function assets(bool $watch = false): void
{
    io()->title('Compiling assets');

    $command = [
        'bin/console',
        'tailwind:build',
    ];

    if ($watch) {
        $command[] = '--watch';
    }

    docker_compose_exec($command);

    io()->success('Done!');
}

#[AsTask(namespace: 'symfony', description: 'Connect to the FrankenPHP container', aliases: ['bash'])]
function bash(): void
{
    io()->title('Connecting to FrankenPHP container');

    $c = context()
        ->withTimeout(null)
        ->withAllowFailure()
    ;

    docker_compose_run('bash', c: $c);
}

#[AsTask(namespace: 'symfony', description: 'Purge all Symfony cache and logs', aliases: ['purge'])]
function purge(): void
{
    io()->title('Purging Symfony cache and logs');

    if (!io()->confirm('Are you sure?', false)) {
        io()->warning('Aborted.');
        return;
    }

    fs()->remove('./public/assets/');
    fs()->remove('./var/cache/');
    fs()->remove('./var/logs/');
    fs()->remove('./build/coverage/coverage-html');

    io()->success('Done!');
}

#[AsTask(namespace: 'database', description: 'Reload all data in database', aliases: ['db:reload'])]
function reload(): void
{
    io()->info('Reloading database...');
    docker_compose_run('bin/console doctrine:database:drop --force --if-exists');
    docker_compose_run('bin/console doctrine:database:create --if-not-exists');
    docker_compose_run('bin/console doctrine:migrations:migrate --no-interaction');

    io()->success('Done!');
}

#[AsTask(namespace: 'database', description: 'Load data fixtures', aliases: ['db:seed'])]
function seed(): void
{
    io()->info('Loading fixtures...');
    docker_compose_exec('bin/console doctrine:fixtures:load --no-interaction');

    io()->success('Done!');
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
    io()->title('Switching to the production environment');
    if (io()->confirm('Are you sure you want to switch to the production environment? This will overwrite your .env.local file.', false)) {
        fs()->copy('.env.local.dist', '.env.local');
        docker_compose_run('bin/console tailwind:build --minify');
        docker_compose_run('bin/console asset-map:compile');

        io()->success('Done!');

        return;
    }

    io()->warning('Aborted.');
}

#[AsTask(namespace: 'symfony', description: 'Switch to the development environment', aliases: ['dev'])]
function dev(): void
{
    io()->title('Switching to the development environment');
    if (io()->confirm('Are you sure you want to switch to the development environment? This will delete your .env.local file.', false)) {
        fs()->remove('.env.local');
        fs()->remove('./public/assets/');

        io()->success('Done!');

        return;
    }

    io()->warning('Aborted.');
}

#[AsTask(namespace: 'cache', description: 'Clear Symfony cache')]
function clear(): void
{
    io()->info('Clearing Symfony cache');
    docker_compose_exec('bin/console cache:clear');

    io()->success('Done!');
}

#[AsTask(namespace: 'log', description: 'Tail Symfony logs')]
function tail(): void
{
    io()->info('Symfony logs lives...');
    $c = context()
        ->withTty(true)
        ->withTimeout(null);

    docker_compose_exec('tail -f var/log/dev.log', c: $c);
}
