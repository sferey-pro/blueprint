<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Domain\ValueObject;

use App\Business\Shared\Domain\Port\UuidFactoryInterface;
use App\Business\Shared\Domain\ValueObject\AbstractUid;
use App\Business\Shared\Infrastructure\Adapter\Symfony\SymfonyUuid;
use App\Tests\Faker\FakerUuidFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('kernel')]
#[CoversClass(AbstractUid::class)]
final class AbstractUidTest extends TestCase
{
    private UuidFactoryInterface $uuidFactory;

    protected function setUp(): void
    {
        $this->uuidFactory = new FakerUuidFactory();
    }

    public function testGenerateCreatesInstance(): void
    {
        $uid = $this->uuidFactory->generate(DummyUid::class);

        self::assertInstanceOf(DummyUid::class, $uid);
        self::assertInstanceOf(SymfonyUuid::class, $uid->value);
    }

    public function testFromStringCreatesInstance(): void
    {
        $uuidString = 'a72855f3-3361-41d3-8515-0181792b0efc';
        $uid = $this->uuidFactory->fromString(DummyUid::class, $uuidString);

        self::assertSame($uuidString, (string) $uid);
    }

    public function testEquals(): void
    {
        $uuidString = 'a72855f3-3361-41d3-8515-0181792b0efc';
        $uid1 = $this->uuidFactory->fromString(DummyUid::class, $uuidString);
        $uid2 = $this->uuidFactory->fromString(DummyUid::class, $uuidString);
        $uid3 = $this->uuidFactory->generate(DummyUid::class);

        self::assertTrue($uid1->equals($uid2));
        self::assertFalse($uid1->equals($uid3));
    }

    public function testValueReturnsUnderlyingUid(): void
    {
        $uuidString = 'a72855f3-3361-41d3-8515-0181792b0efc';
        $uid = $this->uuidFactory->fromString(DummyUid::class, $uuidString);

        self::assertInstanceOf(SymfonyUuid::class, $uid->value());
        self::assertSame($uuidString, (string) $uid->value());
    }

    public function testToArray(): void
    {
        $uuidString = 'a72855f3-3361-41d3-8515-0181792b0efc';
        $uid = $this->uuidFactory->fromString(DummyUid::class, $uuidString);

        $expectedArray = ['value' => $uuidString];

        self::assertSame($expectedArray, $uid->__toArray());
    }

    #[DataProvider('provideArrayData')]
    public function testFromArrayAlwaysThrowsLogicException(array $data): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('DummyUid cannot be created from an array directly. Use a factory.');

        DummyUid::fromArray($data);
    }

    public static function provideArrayData(): \Generator
    {
        yield 'valid data' => [['value' => 'a72855f3-3361-41d3-8515-0181792b0efc']];
        yield 'missing data' => [[]];
        yield 'invalid data' => [['value' => 123]];
    }
}
