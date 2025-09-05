<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Domain\ValueObject;

use App\Business\Shared\Domain\Exception\ValidationException;
use App\Business\Shared\Domain\ValueObject\PhoneNumber;
use PHPUnit\Framework\Attributes\{CoversClass, DataProvider, Group};
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

#[Group('unit')]
#[Group('shared')]
#[CoversClass(PhoneNumber::class)]
final class PhoneNumberTest extends TestCase
{
    /**
     * @param non-empty-string $validInput
     * @param non-empty-string $expectedNormalizedValue
     */
    #[DataProvider('provideValidPhoneNumbers')]
    public function testCreateWithValidPhoneNumber(string $validInput, string $expectedNormalizedValue): void
    {
        $result = PhoneNumber::create($validInput);

        self::assertTrue($result->isSuccess());

        $phoneNumber = $result->value();
        self::assertInstanceOf(PhoneNumber::class, $phoneNumber);
        self::assertSame($expectedNormalizedValue, $phoneNumber->value());
    }

    public static function provideValidPhoneNumbers(): \Generator
    {
        yield 'simple digits' => ['0123456789', '0123456789'];
        yield 'with plus sign' => ['+33123456789', '+33123456789'];
        yield 'with spaces and dashes' => ['+33 (0)1 23-45-67-89', '+330123456789'];
        yield 'local number with formatting' => ['(04) 12 34 56 78', '0412345678'];
        yield 'number with leading/trailing spaces' => ['  +33 123 456 789  ', '+33123456789'];
    }

    /**
     * @param non-empty-string $invalidValue
     */
    #[DataProvider('provideInvalidPhoneNumbers')]
    public function testCreateWithInvalidPhoneNumber(string $invalidValue, string $expectedExceptionMessage): void
    {
        $result = PhoneNumber::create($invalidValue);

        self::assertTrue($result->isFailure());

        $error = $result->error();
        self::assertInstanceOf(ValidationException::class, $error);
        self::assertInstanceOf(InvalidArgumentException::class, $error->getPrevious());
        self::assertStringContainsString($expectedExceptionMessage, $error->getPrevious()->getMessage());
    }

    public static function provideInvalidPhoneNumbers(): \Generator
    {
        yield 'empty phone number' => ['', 'Phone number cannot be empty.'];
        yield 'too long' => ['+12345678901234567890', 'Phone number cannot exceed 15 characters.'];
        yield 'invalid characters' => ['+33-abc-123', 'Phone number "+33-abc-123" is not valid.'];
        yield 'plus sign in the middle' => ['01+23456789', 'Phone number "01+23456789" is not valid.'];
    }

    public function testEquals(): void
    {
        $phone1 = PhoneNumber::fromValidatedValue('+33 (1) 23 45 67 89');
        $phone2 = PhoneNumber::fromValidatedValue('+33123456789'); // Normalisé, donc identique
        $phone3 = PhoneNumber::fromValidatedValue('0123456789');

        self::assertTrue($phone1->equals($phone2));
        self::assertFalse($phone1->equals($phone3));
    }
}
