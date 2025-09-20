<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Query;

use App\Kernel\Attribute\AsQueryHandler;

#[AsQueryHandler]
final readonly class FindGreetingQueryHandler
{
    public function __construct(
        private GreetingFinderInterface $finder,
    ) {
    }

    public function __invoke(FindGreetingQuery $query): GreetingView
    {
        return $this->finder->get($query->id);
    }
}
