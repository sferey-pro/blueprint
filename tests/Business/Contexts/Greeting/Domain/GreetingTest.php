<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Domain;

use App\Business\Contexts\Greeting\Domain\Greeting;
use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(Greeting::class)]
final class GreetingTest extends TestCase
{
    public function testCreate(): void
    {
        // 1. Arrange
        $message = 'Hello, DDD!';
        $createdAt = new \DateTimeImmutable('2023-10-27 10:00:00');

        // 2. Act
        $greeting = Greeting::create($message, $createdAt);

        // 3. Assert
        self::assertInstanceOf(Greeting::class, $greeting);
        self::assertInstanceOf(GreetingId::class, $greeting->id);
        self::assertSame($message, $greeting->message());
        self::assertSame($createdAt, $greeting->createdAt);
    }
}
