<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Domain;

use App\Business\Contexts\Greeting\Domain\Event\GreetingWasCreated;
use App\Business\Contexts\Greeting\Domain\Event\GreetingWasPublished;
use App\Business\Contexts\Greeting\Domain\Greeting;
use App\Business\Contexts\Greeting\Domain\GreetingStatus;
use App\Business\Contexts\Greeting\Domain\ValueObject\Author;
use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Business\Shared\Domain\ValueObject\Email;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(Greeting::class)]
final class GreetingTest extends TestCase
{
    public function testCreateSetsInitialStateAndRaisesEvent(): void
    {
        // 1. Arrange
        $message = 'Hello, DDD!';
        $email = Email::fromValidatedValue('test@example.com');
        $businessCreatedAt = new \DateTimeImmutable('2023-10-27 10:00:00');
        $clock = new MockClock('2025-01-01 12:00:00'); // L'heure système est contrôlée

        // 2. Act
        $greeting = Greeting::create(
            $message,
            Author::create($email),
            $businessCreatedAt,
            $clock
        );

        // 3. Assert
        self::assertInstanceOf(Greeting::class, $greeting);
        self::assertInstanceOf(GreetingId::class, $greeting->id);
        self::assertSame($message, $greeting->message);
        self::assertSame($email, $greeting->author->email);
        self::assertSame($businessCreatedAt, $greeting->createdAt);
        self::assertSame(GreetingStatus::DRAFT, $greeting->status, 'A new greeting should be in DRAFT status.');

        $events = $greeting->pullDomainEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(GreetingWasCreated::class, $events[0]);

        /** @var GreetingWasCreated $event */
        $event = $events[0];
        self::assertSame($message, $event->message);
        self::assertEquals($clock->now(), $event->occurredOn);
    }

    public function testPublishChangesStatusAndRaisesEvent(): void
    {
        // 1. Arrange
        $clock = new MockClock();
        $greeting = Greeting::create(
            'A message to publish',
            Author::create(Email::fromValidatedValue('test@example.com')),
            $clock->now(),
            $clock
        );
        $greeting->pullDomainEvents();

        // 2. Act
        $clock->modify('+10 seconds');
        $greeting->publish($clock);

        // 3. Assert
        self::assertSame(GreetingStatus::PUBLISHED, $greeting->status);

        $events = $greeting->pullDomainEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(GreetingWasPublished::class, $events[0]);

        /** @var GreetingWasPublished $event */
        $event = $events[0];
        self::assertTrue($greeting->id->equals($event->aggregateId));
        self::assertEquals($clock->now(), $event->occurredOn);
    }
}
