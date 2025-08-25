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
        $greeting = Greeting::create(
            $command->message,
            $this->clock->now()
        );

        $this->repository->add($greeting);
    }
}
