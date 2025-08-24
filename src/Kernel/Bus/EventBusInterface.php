<?php

declare(strict_types=1);

namespace App\Kernel\Bus;

use App\Kernel\Bus\Message\DomainEvent;

interface EventBusInterface
{
    /**
     * @template T
     *
     * @param DomainEvent<T> $event
     */
    public function dispatch(DomainEvent $event): void;
}
