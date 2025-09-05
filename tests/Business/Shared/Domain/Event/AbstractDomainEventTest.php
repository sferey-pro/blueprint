<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Domain\Event;

use App\Business\Shared\Domain\Event\AbstractDomainEvent;
use App\Business\Shared\Domain\Port\UuidFactoryInterface;
use App\Business\Shared\Domain\ValueObject\{AggregateRootId, EventId};
use App\Tests\Faker\FakerUuidFactory;
use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('kernel')]
#[CoversClass(AbstractDomainEvent::class)]
final class AbstractDomainEventTest extends TestCase
{
    private UuidFactoryInterface $uuidFactory;

    protected function setUp(): void
    {
        $this->uuidFactory = new FakerUuidFactory();
    }

    public function testConstructorAndGettersWorkAsExpected(): void
    {
        // 1. Arrange
        $eventId = $this->uuidFactory->generate(EventId::class);
        $aggregateId = $this->uuidFactory->generate(DummyAggregateRootId::class);
        $occurredOn = new \DateTimeImmutable();

        // 2. Act
        $event = new readonly class($eventId, $aggregateId, $occurredOn) extends AbstractDomainEvent {
            public static function eventName(): string
            {
                return 'dummy.event';
            }
        };

        // 3. Assert
        self::assertInstanceOf(EventId::class, $event->eventId);
        self::assertTrue($eventId->equals($event->eventId));
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
