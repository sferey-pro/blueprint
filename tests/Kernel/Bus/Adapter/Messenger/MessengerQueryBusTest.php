<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Bus\Adapter\Messenger;

use App\Kernel\Bus\Adapter\Messenger\MessengerQueryBus;
use App\Kernel\Bus\Message\Query;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[Group('unit')]
#[Group('kernel')]
#[CoversClass(MessengerQueryBus::class)]
final class MessengerQueryBusTest extends TestCase
{
    public function testAskSuccessReturnsResult(): void
    {
        // 1. Arrange
        $query = $this->createMock(Query::class);
        $expectedResult = ['data' => 'some result']; // Le résultat attendu de la requête

        // On crée un Envelope qui contient un HandledStamp avec le résultat attendu.
        $envelope = new Envelope($query, [new HandledStamp($expectedResult, 'DummyHandler')]);

        $messengerBusMock = $this->createMock(MessageBusInterface::class);
        $messengerBusMock
            ->expects(self::once())
            ->method('dispatch')
            ->with($query)
            ->willReturn($envelope);

        // 2. Act
        $queryBus = new MessengerQueryBus($messengerBusMock);
        $actualResult = $queryBus->ask($query);

        // 3. Assert
        // On vérifie que la méthode ask() a bien retourné le résultat contenu dans le HandledStamp.
        self::assertSame($expectedResult, $actualResult);
    }

    public function testAskUnwrapsHandlerFailedException(): void
    {
        // 3. Assert
        $originalException = new \DomainException('Erreur de lecture.');
        $this->expectExceptionObject($originalException);

        // 1. Arrange
        $query = $this->createMock(Query::class);
        $envelope = new Envelope($query);
        $handlerFailedException = new HandlerFailedException($envelope, [$originalException]);

        $messengerBusMock = $this->createMock(MessageBusInterface::class);
        $messengerBusMock
            ->expects(self::once())
            ->method('dispatch')
            ->with($query)
            ->willThrowException($handlerFailedException);

        // 2. Act
        $queryBus = new MessengerQueryBus($messengerBusMock);
        $queryBus->ask($query);
    }
}
