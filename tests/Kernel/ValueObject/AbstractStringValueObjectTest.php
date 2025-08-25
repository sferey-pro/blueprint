<?php

declare(strict_types=1);

namespace App\Tests\Kernel\ValueObject;

use App\Kernel\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Kernel\ValueObject\AbstractStringValueObject
 * @covers \App\Kernel\ValueObject\ValidatedValueObjectTrait
 *
 * @internal
 */
final class AbstractStringValueObjectTest extends TestCase
{
    public function testCreateWithValidValueReturnsSuccessResult(): void
    {
        $result = DummyStringVO::create('valid value');

        self::assertTrue($result->isSuccess());
        self::assertInstanceOf(DummyStringVO::class, $result->value());
        self::assertSame('valid value', $result->value()->value());
    }

    public function testCreateWithInvalidValueReturnsFailureResult(): void
    {
        $result = DummyStringVO::create('');

        self::assertTrue($result->isFailure());
        self::assertInstanceOf(ValidationException::class, $result->error());
    }

    public function testFromValidatedValueWithValidValueReturnsInstance(): void
    {
        $vo = DummyStringVO::fromValidatedValue('valid value');

        self::assertSame('valid value', $vo->value());
    }

    public function testFromValidatedValueWithInvalidValueThrowsException(): void
    {
        $this->expectException(ValidationException::class);

        DummyStringVO::fromValidatedValue('');
    }

    public function testEquals(): void
    {
        $vo1 = DummyStringVO::fromValidatedValue('same');
        $vo2 = DummyStringVO::fromValidatedValue('same');
        $vo3 = DummyStringVO::fromValidatedValue('different');

        self::assertTrue($vo1->equals($vo2));
        self::assertFalse($vo1->equals($vo3));
    }

    public function testToString(): void
    {
        $vo = DummyStringVO::fromValidatedValue('hello');

        self::assertSame('hello', (string) $vo);
    }

    public function testToArray(): void
    {
        $vo = DummyStringVo::fromValidatedValue('test value');
        $expected = ['value' => 'test value'];

        self::assertSame($expected, $vo->__toArray());
    }

    public function testFromArrayWithValidData(): void
    {
        $data = ['value' => 'test value'];
        $vo = DummyStringVo::fromArray($data);

        self::assertInstanceOf(DummyStringVo::class, $vo);
        self::assertSame('test value', $vo->value());
    }

    public function testFromArrayWithMissingDataThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required fields: value');

        DummyStringVo::fromArray([]);
    }
}
