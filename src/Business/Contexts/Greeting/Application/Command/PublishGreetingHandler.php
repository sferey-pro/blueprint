<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Command;

use App\Business\Contexts\Greeting\Domain\GreetingRepositoryInterface;
use App\Business\Shared\Domain\Port\UuidFactoryInterface;
use App\Kernel\Attribute\AsCommandHandler;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsCommandHandler]
final readonly class PublishGreetingHandler
{
    public function __construct(
        private UuidFactoryInterface $uuidFactory,
        private ClockInterface $clock,
        private GreetingRepositoryInterface $repository,
        #[Autowire(service: 'state_machine.greeting_publishing')]
        private WorkflowInterface $workflow,
    ) {
    }

    public function __invoke(PublishGreetingCommand $command): void
    {
        $greeting = $this->repository->ofId($command->greetingId);

        if (null === $greeting) {
            // Idéalement, lever une exception métier explicite.
            throw new \RuntimeException('Greeting not found.');
        }

        // On vérifie que la transition est possible
        if (!$this->workflow->can($greeting, 'publish')) {
            throw new \RuntimeException('Cannot publish this greeting.');
        }

        // 2. On demande à l'agrégat d'exécuter le comportement métier
        $greeting->publish($this->uuidFactory, $this->clock);

        // 3. On persiste l'état modifié de l'agrégat.
        // La transaction du bus de commande s'occupera du flush.
    }
}
