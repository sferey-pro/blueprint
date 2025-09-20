<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Business\Contexts\Greeting\Application\Query\{FindGreetingQuery, GreetingView};
use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Business\Shared\Domain\Port\UuidFactoryInterface;
use App\Kernel\Bus\QueryBusInterface;

/**
 * @implements ProviderInterface<GreetingView>
 */
final readonly class GreetingItemProvider implements ProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?GreetingView
    {
        /** @var string $id */
        $id = $uriVariables['id'];

        $model = $this->queryBus->ask(
            new FindGreetingQuery(
                $this->uuidFactory->fromString(GreetingId::class, $id)
            )
        );

        return $model;
    }
}
