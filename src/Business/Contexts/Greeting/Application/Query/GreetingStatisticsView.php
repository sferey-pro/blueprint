<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Query;

/**
 * DTO pour les statistiques des Greetings.
 */
final readonly class GreetingStatisticsView
{
    public function __construct(
        public int $total,
        public int $draftCount,
        public int $publishedCount,
    ) {
    }
}
