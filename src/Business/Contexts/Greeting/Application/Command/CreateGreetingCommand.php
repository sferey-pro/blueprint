<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Command;

use App\Business\Contexts\Greeting\Application\CreateGreetingHandler;
use App\Kernel\Bus\Message\Command;

/**
 * @template-implements Command<CreateGreetingHandler>
 */
final readonly class CreateGreetingCommand implements Command
{
    public function __construct(
        public string $message,
    ) {
    }
}
