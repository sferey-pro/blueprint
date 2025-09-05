<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Domain\ValueObject;

use App\Business\Shared\Domain\Exception\ValidationException;
use App\Business\Shared\Domain\ValueObject\Email;
use PHPUnit\Framework\Attributes\{CoversClass, DataProvider, Group};
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

#[Group('unit')]
#[Group('shared')]
#[CoversClass(Email::class)]
final class EmailTest extends TestCase
{
    public function testCreateWithValidEmail(): void
    {
        $result = Email::create('TEST@example.com');

        self::assertTrue($result->isSuccess());

        $email = $result->value();
        self::assertInstanceOf(Email::class, $email);
        self::assertSame('test@example.com', $email->value());
    }

    /**
     * @param non-empty-string $invalidValue
     */
    #[DataProvider('provideInvalidEmails')]
    public function testCreateWithInvalidEmail(string $invalidValue, string $expectedExceptionMessage): void
    {
        $result = Email::create($invalidValue);

        self::assertTrue($result->isFailure());

        $error = $result->error();
        self::assertInstanceOf(ValidationException::class, $error);
        self::assertInstanceOf(InvalidArgumentException::class, $error->getPrevious());
        self::assertStringContainsString($expectedExceptionMessage, $error->getPrevious()->getMessage());
    }

    public static function provideInvalidEmails(): \Generator
    {
        yield 'empty email' => ['', 'Email address cannot be empty'];
        yield 'whitespace only' => [' ', 'Email address cannot be empty'];
        yield 'invalid format' => ['not-an-email', '"not-an-email" is not a valid email address'];
        yield 'email too long' => [str_repeat('a', 180).'@example.com', 'Email address cannot exceed 180 characters'];
        yield 'domain is empty' => ['test@', '"test@" is not a valid email address'];
        yield 'invalid domain format' => ['test@.com', '"test@.com" is not a valid email address'];
    }

    public function testEquals(): void
    {
        $email1 = Email::fromValidatedValue('user@example.com');
        $email2 = Email::fromValidatedValue('USER@example.com');
        $email3 = Email::fromValidatedValue('another@example.com');

        self::assertTrue($email1->equals($email2));
        self::assertFalse($email1->equals($email3));
    }
}
