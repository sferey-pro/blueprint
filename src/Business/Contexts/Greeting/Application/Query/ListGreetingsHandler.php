<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Query;

use App\Kernel\Attribute\AsQueryHandler;

#[AsQueryHandler]
final readonly class ListGreetingsHandler
{
    public function __construct(
        private GreetingFinderInterface $finder,
    ) {
    }

    /**
     * @return list<GreetingView>
     */
    public function __invoke(ListGreetingsQuery $query): array
    {
        return $this->finder->findAllAsView();
    }
}
