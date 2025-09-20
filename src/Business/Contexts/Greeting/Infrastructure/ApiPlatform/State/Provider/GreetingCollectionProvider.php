<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Business\Contexts\Greeting\Application\Query\{GreetingFinderInterface, ListGreetingsQuery};
use App\Kernel\Bus\QueryBusInterface;

/**
 * @implements ProviderInterface<GreetingView>
 */
final class GreetingCollectionProvider implements ProviderInterface
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly GreetingFinderInterface $greetingFinder,
    ) {
    }

    /**
     * @return list<GreetingView>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->queryBus->ask(new ListGreetingsQuery());
    }
}
