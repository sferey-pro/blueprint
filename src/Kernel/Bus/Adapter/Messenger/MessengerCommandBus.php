<?php

declare(strict_types=1);

namespace App\Kernel\Bus\Adapter\Messenger;

use App\Kernel\Bus\CommandBusInterface;
use App\Kernel\Bus\Message\Command;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\{HandleTrait, MessageBusInterface};

final class MessengerCommandBus implements CommandBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    public function dispatch(Command $command): void
    {
        try {
            $this->handle($command);
        } catch (HandlerFailedException $e) {
            if ($exception = current($e->getWrappedExceptions())) {
                throw $exception;
            }

            throw $e;
        }
    }
}
