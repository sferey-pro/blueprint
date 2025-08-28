<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Command;

use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Kernel\Bus\Message\Command;

/**
 * Commande représentant l'intention de publier un Greeting.
 */
final readonly class PublishGreetingCommand implements Command
{
    public function __construct(
        public GreetingId $greetingId,
    ) {
    }
}
