<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Bus\Adapter\Messenger;

use App\Kernel\Bus\Adapter\Messenger\MessengerCommandBus;
use App\Kernel\Bus\Message\Command;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[Group('unit')]
#[Group('kernel')]
#[CoversClass(MessengerCommandBus::class)]
final class MessengerCommandBusTest extends TestCase
{
    public function testDispatchSuccess(): void
    {
        // 1. Arrange
        $command = $this->createMock(Command::class);

        // C'est la simulation d'un message qui a été traité avec succès par un handler.
        $resultFromHandler = 'some_result';
        $envelope = new Envelope($command, [new HandledStamp($resultFromHandler, 'DummyHandler')]);

        $messengerBusMock = $this->createMock(MessageBusInterface::class);
        $messengerBusMock
            ->expects(self::once())
            ->method('dispatch')
            ->with($command)
            ->willReturn($envelope);

        // 2. Act
        $commandBus = new MessengerCommandBus($messengerBusMock);
        $commandBus->dispatch($command);

        // 3. Assert (l'attente sur le mock et l'absence d'exception valident le test)
    }

    public function testDispatchUnwrapsHandlerFailedException(): void
    {
        // 3. Assert
        $originalException = new \DomainException('Ceci est une erreur métier.');
        $this->expectExceptionObject($originalException);

        // 1. Arrange
        $command = $this->createMock(Command::class);
        $envelope = new Envelope($command);
        $handlerFailedException = new HandlerFailedException($envelope, [$originalException]);

        $messengerBusMock = $this->createMock(MessageBusInterface::class);
        $messengerBusMock
            ->expects(self::once())
            ->method('dispatch')
            ->with($command)
            ->willThrowException($handlerFailedException);

        // 2. Act
        $commandBus = new MessengerCommandBus($messengerBusMock);
        $commandBus->dispatch($command);
    }
}
