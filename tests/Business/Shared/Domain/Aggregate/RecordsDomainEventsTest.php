<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Domain\Aggregate;

use App\Business\Shared\Domain\Aggregate\RecordsDomainEvents;
use PHPUnit\Framework\Attributes\{CoversTrait, Group};
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('shared-kernel')]
#[CoversTrait(RecordsDomainEvents::class)]
final class RecordsDomainEventsTest extends TestCase
{
    public function testPullDomainEventsReturnsAndClearsRecordedEvents(): void
    {
        // 1. Arrange
        $harness = new class {
            use RecordsDomainEvents;

            public function doSomething(): void
            {
                $this->raise(new \stdClass());
                $this->raise(new \stdClass());
            }
        };
        $harness->doSomething();

        // 2. Act
        $events = $harness->pullDomainEvents();

        // 3. Assert
        self::assertCount(2, $events, 'Should return all recorded events.');

        $remainingEvents = $harness->pullDomainEvents();
        self::assertCount(0, $remainingEvents, 'Should clear the events list after pulling.');
    }

    public function testEraseRecordedDomainEventClearsEvents(): void
    {
        // 1. Arrange
        $harness = new class {
            use RecordsDomainEvents;

            public function __construct()
            {
                $this->raise(new \stdClass());
            }
        };

        // 2. Act
        $harness->eraseRecordedDomainEvent();
        $events = $harness->pullDomainEvents();

        // 3. Assert
        self::assertCount(0, $events);
    }

    public function testPullDomainEventsOnNewObjectReturnsEmptyArray(): void
    {
        // 1. Arrange
        $harness = new class {
            use RecordsDomainEvents;
        };

        // 2. Act & Assert
        self::assertCount(0, $harness->pullDomainEvents());
    }
}
