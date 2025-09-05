<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Infrastructure\Adapter\Symfony;

use App\Business\Shared\Infrastructure\Adapter\Symfony\SymfonyUuidFactory;
use App\Tests\Business\Shared\Domain\ValueObject\DummyUid;
use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('shared')]
#[CoversClass(SymfonyUuidFactory::class)]
final class SymfonyUuidFactoryTest extends TestCase
{
    private SymfonyUuidFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new SymfonyUuidFactory();
    }

    public function testGenerateCreatesInstanceOfCorrectClass(): void
    {
        // Act
        $uid = $this->factory->generate(DummyUid::class);

        // Assert
        self::assertInstanceOf(DummyUid::class, $uid);
    }

    public function testFromStringCreatesInstance(): void
    {
        // Arrange
        $uuidString = '018f3b8b-9b48-7318-a3de-8c8d1f8f3b8b';

        // Act
        $uid = $this->factory->fromString(DummyUid::class, $uuidString);

        // Assert
        self::assertInstanceOf(DummyUid::class, $uid);
        self::assertSame($uuidString, (string) $uid);
    }

    public function testFromStringThrowsExceptionForInvalidUidClass(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected an instance of/');

        // Arrange
        $uuidString = '018f3b8b-9b48-7318-a3de-8c8d1f8f3b8b';

        // Act
        $this->factory->fromString(\stdClass::class, $uuidString);
    }
}
