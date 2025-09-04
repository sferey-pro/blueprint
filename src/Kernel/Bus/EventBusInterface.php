<?php

declare(strict_types=1);

namespace App\Kernel\Bus;

use App\Kernel\Bus\Message\Message;

interface EventBusInterface
{
    /**
     * Dispatche un message/événement sur le bus.
     * Le message peut être de n'importe quel type.
     *
     * @template T
     *
     * @param object<T> $event
     */
    public function dispatch(object $event): void;
}
