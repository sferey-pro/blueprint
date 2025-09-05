<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Infrastructure\Command;

use App\Business\Contexts\Greeting\Application\Command\CreateGreetingCommand;
use App\Business\Shared\Domain\Exception\ValidationException;
use App\Kernel\Bus\CommandBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'greeting:create',
    description: 'Crée un nouveau message de salutation (Greeting).',
)]
final class CreateGreetingCliCommand extends Command
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('message', InputArgument::REQUIRED, 'Le message de salutation.')
             ->addArgument('author', InputArgument::REQUIRED, 'L\'email de l\'auteur.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $message */
        $message = $input->getArgument('message');
        /** @var string $authorEmail */
        $authorEmail = $input->getArgument('author');

        try {
            $command = new CreateGreetingCommand($message, $authorEmail);
            $this->commandBus->dispatch($command);
        } catch (ValidationException $e) {
            $io->error(\sprintf('Erreur de validation : %s', $e->getPrevious()?->getMessage() ?? $e->getMessage()));

            return Command::FAILURE;
        } catch (\Throwable $e) {
            $io->error(\sprintf('Une erreur est survenue : %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $io->success('Le message de salutation a été créé avec succès !');

        return Command::SUCCESS;
    }
}
