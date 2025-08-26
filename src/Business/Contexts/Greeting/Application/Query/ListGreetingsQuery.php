<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Query;

use App\Kernel\Bus\Message\Query;

/**
 * @implements Query<list<GreetingView>>
 */
final readonly class ListGreetingsQuery implements Query
{
}
