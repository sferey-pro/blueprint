<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Infrastructure\Command;

use App\Business\Contexts\Greeting\Application\Command\PublishGreetingCommand;
use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Kernel\Bus\CommandBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'greeting:publish',
    description: 'Publie un message de salutation existant.',
)]
final class PublishGreetingCliCommand extends Command
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'L\'ID du message à publier.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $id */
        $id = $input->getArgument('id');

        try {
            $command = new PublishGreetingCommand(GreetingId::fromString($id));
            $this->commandBus->dispatch($command);
        } catch (\Throwable $e) {
            $io->error(\sprintf('Une erreur est survenue : %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $io->success('Le message de salutation a été publié avec succès !');

        return Command::SUCCESS;
    }
}
