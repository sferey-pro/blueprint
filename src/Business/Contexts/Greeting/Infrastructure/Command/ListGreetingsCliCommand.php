<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Infrastructure\Command;

use App\Business\Contexts\Greeting\Application\Query\GreetingView;
use App\Business\Contexts\Greeting\Application\Query\ListGreetingsQuery;
use App\Kernel\Bus\QueryBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'greeting:list',
    description: 'Liste tous les messages de salutation.',
)]
final class ListGreetingsCliCommand extends Command
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            /** @var list<GreetingView> $greetings */
            $greetings = $this->queryBus->ask(new ListGreetingsQuery());

            if (empty($greetings)) {
                $io->info('Aucun message de salutation à afficher.');

                return Command::SUCCESS;
            }

            $tableRows = array_map(
                static fn (GreetingView $greeting): array => [$greeting->id, $greeting->status->getLabel(), $greeting->message, $greeting->createdAt],
                $greetings
            );

            $io->table(['ID', 'Status', 'Message', 'Créé le'], $tableRows);
        } catch (\Throwable $e) {
            $io->error(\sprintf('Une erreur est survenue : %s', $e->getMessage()));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
