<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Query;

use App\Kernel\Attribute\AsQueryHandler;

#[AsQueryHandler]
final readonly class GetGreetingStatisticsHandler
{
    public function __construct(
        private GreetingFinderInterface $finder,
    ) {
    }

    public function __invoke(GetGreetingStatisticsQuery $query): GreetingStatisticsView
    {
        return $this->finder->getStatistics();
    }
}
