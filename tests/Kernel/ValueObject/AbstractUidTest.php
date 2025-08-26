<?php

declare(strict_types=1);

namespace App\Tests\Kernel\ValueObject;

use App\Kernel\ValueObject\AbstractUid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[Group('unit')]
#[Group('kernel')]
#[CoversClass(AbstractUid::class)]
final class AbstractUidTest extends TestCase
{
    public function testGenerateCreatesInstance(): void
    {
        $uid = DummyUid::generate();

        self::assertInstanceOf(DummyUid::class, $uid);
        self::assertInstanceOf(Uuid::class, $uid->value);
    }

    public function testFromStringCreatesInstance(): void
    {
        $uuidString = 'a72855f3-3361-41d3-8515-0181792b0efc';
        $uid = DummyUid::fromString($uuidString);

        self::assertSame($uuidString, (string) $uid);
    }

    public function testEquals(): void
    {
        $uuidString = 'a72855f3-3361-41d3-8515-0181792b0efc';
        $uid1 = DummyUid::fromString($uuidString);
        $uid2 = DummyUid::fromString($uuidString);
        $uid3 = DummyUid::generate();

        self::assertTrue($uid1->equals($uid2));
        self::assertFalse($uid1->equals($uid3));
    }

    public function testValueReturnsUnderlyingUid(): void
    {
        $uuidString = 'a72855f3-3361-41d3-8515-0181792b0efc';
        $uid = DummyUid::fromString($uuidString);

        self::assertInstanceOf(\Symfony\Component\Uid\AbstractUid::class, $uid->value());
        self::assertSame($uuidString, $uid->value()->toRfc4122());
    }

    public function testToArray(): void
    {
        $uuidString = 'a72855f3-3361-41d3-8515-0181792b0efc';
        $uid = DummyUid::fromString($uuidString);

        $expectedArray = ['value' => $uuidString];

        self::assertSame($expectedArray, $uid->__toArray());
    }

    public function testFromArrayWithValidData(): void
    {
        $uuidString = 'a72855f3-3361-41d3-8515-0181792b0efc';
        $data = ['value' => $uuidString];

        $uid = DummyUid::fromArray($data);

        self::assertInstanceOf(DummyUid::class, $uid);
        self::assertSame($uuidString, (string) $uid);
    }

    public function testFromArrayWithMissingDataThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing or invalid required fields: value');

        DummyUid::fromArray([]);
    }

    public function testFromArrayWithInvalidDataThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing or invalid required fields: value');

        DummyUid::fromArray(['value' => 123]); // Not a string
    }
}
