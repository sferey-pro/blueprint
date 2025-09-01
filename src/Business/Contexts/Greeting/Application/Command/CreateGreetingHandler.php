<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Command;

use App\Business\Contexts\Greeting\Domain\Greeting;
use App\Business\Contexts\Greeting\Domain\GreetingRepositoryInterface;
use App\Business\Contexts\Greeting\Domain\ValueObject\Author;
use App\Business\Shared\Domain\ValueObject\Email;
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
        $emailResult = Email::create($command->authorEmail);

        if ($emailResult->isFailure()) {
            throw $emailResult->error();
        }

        // On détermine la date métier : soit celle de la commande, soit "maintenant".
        $businessCreatedAt = $command->createdAt ?? $this->clock->now();

        $author = Author::create($emailResult->value());

        $greeting = Greeting::create(
            $command->message,
            $author,
            $businessCreatedAt,
            $this->clock
        );

        $this->repository->add($greeting);
    }
}
