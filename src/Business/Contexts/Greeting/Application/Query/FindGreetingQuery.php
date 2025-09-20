<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Query;

use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Kernel\Bus\Message\Query;

/**
 * @implements Query<GreetingView>
 */
final readonly class FindGreetingQuery implements Query
{
    public function __construct(
        public GreetingId $id,
    ) {
    }
}
