<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Utility;

use App\Business\Shared\Utility\Result;
use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('kernel')]
#[CoversClass(Result::class)]
final class ResultTest extends TestCase
{
    public function testSuccess(): void
    {
        $value = new \stdClass();
        $result = Result::success($value);

        self::assertTrue($result->isSuccess());
        self::assertFalse($result->isFailure());
        self::assertSame($value, $result->value());
        self::assertSame($value, $result->valueOrThrow());
    }

    public function testFailure(): void
    {
        $error = new \RuntimeException('Something went wrong');
        $result = Result::failure($error);

        self::assertFalse($result->isSuccess());
        self::assertTrue($result->isFailure());
    }

    public function testValueOnFailureThrowsException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot get value from a failure result.');

        $error = new \RuntimeException('Something went wrong');
        $result = Result::failure($error);
        $result->value();
    }

    public function testErrorOnSuccessThrowsException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot get error from a success result.');

        $result = Result::success(new \stdClass());
        $result->error();
    }

    public function testValueOrThrowOnFailureThrowsOriginalException(): void
    {
        $error = new \RuntimeException('Something went wrong');
        $this->expectExceptionObject($error);

        $result = Result::failure($error);
        $result->valueOrThrow();
    }

    public function testValueOr(): void
    {
        $successValue = new \stdClass();
        $successResult = Result::success($successValue);

        $failureResult = Result::failure(new \RuntimeException());
        $defaultValue = new \stdClass();

        self::assertSame($successValue, $successResult->valueOr($defaultValue));
        self::assertSame($defaultValue, $failureResult->valueOr($defaultValue));
    }

    public function testMapOnSuccess(): void
    {
        $initialValue = (object) ['count' => 5];
        $result = Result::success($initialValue);

        $mappedResult = $result->map(static fn (\stdClass $v): \stdClass => (object) ['count' => $v->count * 2]);

        self::assertTrue($mappedResult->isSuccess());
        self::assertEquals((object) ['count' => 10], $mappedResult->value());
    }

    public function testMapOnFailure(): void
    {
        $error = new \RuntimeException();
        $result = Result::failure($error);

        $mappedResult = $result->map(static fn (mixed $v) => $v * 2);

        self::assertTrue($mappedResult->isFailure());
        self::assertSame($error, $mappedResult->error());
    }

    public function testFlatMapOnSuccess(): void
    {
        $initialValue = (object) ['count' => 5];
        $result = Result::success($initialValue);

        $mappedResult = $result->flatMap(static fn (\stdClass $v): Result => Result::success((object) ['count' => $v->count * 2]));

        self::assertTrue($mappedResult->isSuccess());
        self::assertEquals((object) ['count' => 10], $mappedResult->value());
    }

    public function testFlatMapOnFailure(): void
    {
        $error = new \RuntimeException();
        $result = Result::failure($error);

        $mappedResult = $result->flatMap(static fn (\stdClass $v): Result => Result::success((object) ['count' => $v->count * 2]));

        self::assertTrue($mappedResult->isFailure());
        self::assertSame($error, $mappedResult->error());
    }
}
