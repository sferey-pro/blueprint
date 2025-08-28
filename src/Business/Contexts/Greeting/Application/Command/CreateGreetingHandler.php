<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Command;

use App\Business\Contexts\Greeting\Domain\Greeting;
use App\Business\Contexts\Greeting\Domain\GreetingRepositoryInterface;
use App\Kernel\Attribute\AsCommandHandler;
use Psr\Clock\ClockInterface;

#[AsCommandHandler]
final readonly class CreateGreetingHandler
{
    public function __construct(
        private GreetingRepositoryInterface $repository,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(CreateGreetingCommand $command): void
    {
        // On détermine la date métier : soit celle de la commande, soit "maintenant".
        $businessCreatedAt = $command->createdAt ?? $this->clock->now();

        $greeting = Greeting::create(
            $command->message,
            $businessCreatedAt,
            $this->clock
        );

        $this->repository->add($greeting);
    }
}
