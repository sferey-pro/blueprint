<?php

declare(strict_types=1);

namespace App\Kernel\Bus\Adapter\Messenger;

use App\Kernel\Bus\EventBusInterface;
use App\Kernel\Bus\Message\DomainEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerEventBus implements EventBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->messageBus = $eventBus;
    }

    public function dispatch(DomainEvent $event): void
    {
        try {
            $this->handle($event);
        } catch (HandlerFailedException $e) {
            if ($exception = current($e->getWrappedExceptions())) {
                throw $exception;
            }

            throw $e;
        }
    }
}
