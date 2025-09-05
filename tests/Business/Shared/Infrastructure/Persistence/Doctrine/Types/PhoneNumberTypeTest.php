<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Infrastructure\Persistence\Doctrine\Types;

use App\Business\Shared\Domain\ValueObject\PhoneNumber;
use App\Business\Shared\Infrastructure\Persistence\Doctrine\Types\PhoneNumberType;
use App\Tests\Helper\Doctrine\Types\ValueObjectStringTypeTestCase;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\{CoversClass, Group};

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(PhoneNumberType::class)]
final class PhoneNumberTypeTest extends ValueObjectStringTypeTestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!Type::hasType(PhoneNumberType::NAME)) {
            Type::addType(PhoneNumberType::NAME, PhoneNumberType::class);
        }
    }

    protected function getTypeClass(): string
    {
        return PhoneNumberType::class;
    }

    protected function getValueObjectClass(): string
    {
        return PhoneNumber::class;
    }

    protected function getTypeName(): string
    {
        return PhoneNumberType::NAME;
    }

    protected function getValidValue(): string
    {
        return '+33123456789';
    }
}
