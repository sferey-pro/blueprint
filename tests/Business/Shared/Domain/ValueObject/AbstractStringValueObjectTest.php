<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Domain\ValueObject;

use App\Business\Shared\Domain\Exception\ValidationException;
use App\Business\Shared\Domain\ValueObject\AbstractStringValueObject;
use App\Business\Shared\Domain\ValueObject\ValidatedValueObjectTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

#[Group('unit')]
#[Group('kernel')]
#[CoversClass(AbstractStringValueObject::class)]
#[CoversTrait(ValidatedValueObjectTrait::class)]
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

    /**
     * @param list<mixed> $invalidArgs
     */
    #[DataProvider('provideInvalidConstructorArguments')]
    public function testCreateWithInvalidArguments(array $invalidArgs, string $expectedMessage): void
    {
        // 1. Act
        $result = DummyStringVO::create(...$invalidArgs);

        // 2. Assert
        self::assertTrue($result->isFailure(), 'The creation should have failed but it succeeded.');

        $error = $result->error();
        self::assertInstanceOf(ValidationException::class, $error);
        self::assertInstanceOf(InvalidArgumentException::class, $error->getPrevious());
        self::assertStringContainsString($expectedMessage, $error->getPrevious()->getMessage());
    }

    public static function provideInvalidConstructorArguments(): \Generator
    {
        yield 'no arguments' => [
            'invalidArgs' => [],
            'expectedMessage' => 'Value Object expects a single string argument',
        ];

        yield 'too many arguments' => [
            'invalidArgs' => ['value1', 'value2'],
            'expectedMessage' => 'Value Object expects a single string argument',
        ];

        yield 'wrong argument type (int)' => [
            'invalidArgs' => [123],
            'expectedMessage' => 'Value Object expects a single string argument, got "integer"',
        ];

        yield 'wrong argument type (array)' => [
            'invalidArgs' => [['array']],
            'expectedMessage' => 'Value Object expects a single string argument, got "array"',
        ];
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
