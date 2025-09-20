<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Query;

use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;

interface GreetingFinderInterface
{
    public function get(GreetingId $id): ?GreetingView;

    /**
     * @return list<GreetingView>
     */
    public function findAllAsView(): array;

    public function getStatistics(): GreetingStatisticsView;
}
