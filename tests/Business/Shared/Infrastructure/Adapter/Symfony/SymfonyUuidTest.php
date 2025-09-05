<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Infrastructure\Adapter\Symfony;

use App\Business\Shared\Infrastructure\Adapter\Symfony\SymfonyUuid;
use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\{Uuid};

#[Group('unit')]
#[Group('shared')]
#[CoversClass(SymfonyUuid::class)]
final class SymfonyUuidTest extends TestCase
{
    public function testToString(): void
    {
        // Arrange
        $uuidString = '018f3b7b-2e9a-74b4-9694-5c9a0b0d393e';
        $symfonyUuid = Uuid::fromString($uuidString);
        $uuid = new SymfonyUuid($symfonyUuid);

        // Act & Assert
        self::assertSame($uuidString, (string) $uuid);
    }

    public function testToRfc4122(): void
    {
        // Arrange
        $uuidString = '018f3b7b-2e9a-74b4-9694-5c9a0b0d393e';
        $symfonyUuid = Uuid::fromString($uuidString);
        $uuid = new SymfonyUuid($symfonyUuid);

        // Act & Assert
        self::assertSame($uuidString, $uuid->toRfc4122());
    }

    public function testToBinary(): void
    {
        // Arrange
        $uuidString = '018f3b7b-2e9a-74b4-9694-5c9a0b0d393e';
        $symfonyUuid = Uuid::fromString($uuidString);
        $uuid = new SymfonyUuid($symfonyUuid);

        // Act & Assert
        self::assertSame($symfonyUuid->toBinary(), $uuid->toBinary());
    }

    public function testEquals(): void
    {
        // Arrange
        $uuidString = '018f3b7b-2e9a-74b4-9694-5c9a0b0d393e';
        $uuid1 = new SymfonyUuid(Uuid::fromString($uuidString));
        $uuid2 = new SymfonyUuid(Uuid::fromString($uuidString));
        $uuid3 = new SymfonyUuid(Uuid::v7());

        // Act & Assert
        self::assertTrue($uuid1->equals($uuid2));
        self::assertFalse($uuid1->equals($uuid3));
    }

    public function testFromString(): void
    {
        // Arrange
        $uuidString = '018f3b7b-2e9a-74b4-9694-5c9a0b0d393e';

        // Act
        $uuid = SymfonyUuid::fromString($uuidString);

        // Assert
        self::assertInstanceOf(SymfonyUuid::class, $uuid);
        self::assertSame($uuidString, (string) $uuid);
    }

    public function testV7(): void
    {
        // Act
        $uuid = SymfonyUuid::v7();

        // Assert
        self::assertInstanceOf(SymfonyUuid::class, $uuid);
        self::assertTrue(Uuid::isValid((string) $uuid));
    }
}
