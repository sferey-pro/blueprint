<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Bus\Message;

use App\Kernel\Bus\Message\AbstractDomainEvent;
use App\Kernel\ValueObject\AggregateRootId;
use App\Kernel\ValueObject\EventId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('kernel')]
#[CoversClass(AbstractDomainEvent::class)]
final class AbstractDomainEventTest extends TestCase
{
    public function testConstructorAndGettersWorkAsExpected(): void
    {
        // 1. Arrange
        $aggregateId = DummyAggregateRootId::generate();
        $occurredOn = new \DateTimeImmutable();

        // 2. Act
        $event = new readonly class($aggregateId, $occurredOn) extends AbstractDomainEvent {
            public static function eventName(): string
            {
                return 'dummy.event';
            }
        };

        // 3. Assert
        self::assertInstanceOf(EventId::class, $event->eventId);
        self::assertTrue($aggregateId->equals($event->aggregateId));
        self::assertSame($occurredOn, $event->occurredOn);
    }
}

/**
 * Classe de test interne pour instancier un AggregateRootId.
 *
 * @internal
 */
final readonly class DummyAggregateRootId extends AggregateRootId
{
}
