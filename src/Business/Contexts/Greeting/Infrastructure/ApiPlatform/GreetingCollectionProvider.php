<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Infrastructure\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Business\Contexts\Greeting\Application\Query\ListGreetingsQuery;
use App\Kernel\Bus\QueryBusInterface;

final class GreetingCollectionProvider implements ProviderInterface
{
    public function __construct(private readonly QueryBusInterface $queryBus)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->queryBus->ask(new ListGreetingsQuery());
    }
}
