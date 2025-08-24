<?php

declare(strict_types=1);

namespace App\Kernel\Bus;

use App\Kernel\Bus\Message\Command;

interface CommandBusInterface
{
    /**
     * @template T
     *
     * @param Command<T> $command
     */
    public function dispatch(Command $command): void;
}
